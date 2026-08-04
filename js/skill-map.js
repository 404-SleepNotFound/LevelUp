/**
 * LevelUp - Interactive Skill Map Engine
 * Handles Canvas Pan/Zoom, SVG Edge routing, and AJAX integrations.
 */

const skillMapApp = (function() {
    
    // DOM Elements
    const mapWrapper = document.getElementById('mapWrapper');
    const mapCanvas = document.getElementById('mapCanvas');
    const nodesLayer = document.getElementById('nodesLayer');
    const svgLayer = document.getElementById('svgLayer');
    const careerSelector = document.getElementById('careerSelector');
    
    // Left Panel Elements
    const detailPanel = document.getElementById('skillDetailPanel');
    const btnCloseLeft = document.getElementById('btnCloseLeftPanel');
    
    // State
    let nodesData = [];
    let isDragging = false;
    let startX, startY;
    
    // Transform State (Translate X, Y, Scale)
    let transform = { x: 0, y: 0, scale: 1 };
    
    // Bounds & Settings
    const MIN_SCALE = 0.3;
    const MAX_SCALE = 2;
    const SCALE_SENSITIVITY = 0.001;

    // ==========================================
    // 1. Initialization
    // ==========================================
    async function init() {
        bindEvents();
        await loadMapData(careerSelector.value);
        centerMap();
    }

    function bindEvents() {
        // Pan Events (Mouse)
        mapWrapper.addEventListener('mousedown', handleMouseDown);
        window.addEventListener('mousemove', handleMouseMove);
        window.addEventListener('mouseup', handleMouseUp);
        
        // Zoom Events (Wheel)
        mapWrapper.addEventListener('wheel', handleWheel, { passive: false });
        
        // UI Controls
        document.getElementById('btnZoomIn').addEventListener('click', () => zoomBy(0.2));
        document.getElementById('btnZoomOut').addEventListener('click', () => zoomBy(-0.2));
        document.getElementById('btnResetMap').addEventListener('click', centerMap);
        careerSelector.addEventListener('change', (e) => loadMapData(e.target.value));
        
        btnCloseLeft.addEventListener('click', () => {
            detailPanel.classList.add('d-none');
            document.querySelectorAll('.skill-node').forEach(n => n.classList.remove('selected'));
        });
    }

    // ==========================================
    // 2. Data Fetching & Rendering
    // ==========================================
    async function loadMapData(career) {
        // Show Loading (Optional: Add spinner overlay to map)
        
        try {
            const res = await fetch('ajax_skill_map.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_map_data', career: career })
            });
            const data = await res.json();
            
            if(data.status === 'success') {
                nodesData = data.nodes;
                renderGraph();
                updateAiInsights(data.aiInsights);
            }
        } catch (e) {
            console.error("Failed to load map data", e);
        }
    }

    function renderGraph() {
        nodesLayer.innerHTML = '';
        svgLayer.innerHTML = '';
        
        // 1. Draw Edges (Lines) First (so they are behind nodes)
        nodesData.forEach(targetNode => {
            if (targetNode.depends && targetNode.depends.length > 0) {
                targetNode.depends.forEach(sourceId => {
                    const sourceNode = nodesData.find(n => n.id === sourceId);
                    if (sourceNode) drawEdge(sourceNode, targetNode);
                });
            }
        });

        // 2. Draw Nodes (HTML Elements)
        nodesData.forEach(node => {
            const el = document.createElement('div');
            el.className = 'skill-node';
            el.setAttribute('data-id', node.id);
            el.setAttribute('data-status', node.status);
            
            // Positioning via CSS translate
            el.style.left = `${node.x}px`;
            el.style.top = `${node.y}px`;
            
            el.innerText = node.label;
            
            el.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent trigger drag
                handleNodeClick(node.id, el);
            });
            
            nodesLayer.appendChild(el);
        });
    }

    // ==========================================
    // 3. SVG Drawing Logic
    // ==========================================
    function drawEdge(source, target) {
        // Simple straight line using SVG
        const line = document.createElementNS("http://www.w3.org/2000/svg", "line");
        line.setAttribute('x1', source.x);
        line.setAttribute('y1', source.y);
        line.setAttribute('x2', target.x);
        line.setAttribute('y2', target.y);
        line.setAttribute('class', 'skill-edge');
        
        // If target is recommended and source is mastered, highlight line
        if(target.status === 'recommended' && source.status === 'mastered') {
            line.classList.add('active');
        }
        
        svgLayer.appendChild(line);
    }

    // ==========================================
    // 4. Pan & Zoom Engine
    // ==========================================
    function updateTransform() {
        mapCanvas.style.transform = `translate(${transform.x}px, ${transform.y}px) scale(${transform.scale})`;
    }

    function handleMouseDown(e) {
        // Don't drag if clicking on UI elements
        if(e.target.closest('.map-controls') || e.target.closest('.map-legend')) return;
        
        isDragging = true;
        startX = e.clientX - transform.x;
        startY = e.clientY - transform.y;
    }

    function handleMouseMove(e) {
        if (!isDragging) return;
        e.preventDefault();
        transform.x = e.clientX - startX;
        transform.y = e.clientY - startY;
        updateTransform();
    }

    function handleMouseUp() {
        isDragging = false;
    }

    function handleWheel(e) {
        e.preventDefault(); // Prevent page scroll
        
        // Calculate zoom focus point based on mouse position relative to wrapper
        const rect = mapWrapper.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;

        // Calculate zoom factor
        const zoom = Math.exp(-e.deltaY * SCALE_SENSITIVITY);
        let newScale = transform.scale * zoom;
        
        // Clamp scale
        newScale = Math.max(MIN_SCALE, Math.min(newScale, MAX_SCALE));
        
        // Adjust translation to zoom into mouse point
        // Formula: newTx = mouseX - (mouseX - oldTx) * (newScale / oldScale)
        transform.x = mouseX - (mouseX - transform.x) * (newScale / transform.scale);
        transform.y = mouseY - (mouseY - transform.y) * (newScale / transform.scale);
        transform.scale = newScale;
        
        updateTransform();
    }
    
    function zoomBy(factor) {
        let newScale = transform.scale + factor;
        newScale = Math.max(MIN_SCALE, Math.min(newScale, MAX_SCALE));
        
        // Zoom to center of wrapper
        const rect = mapWrapper.getBoundingClientRect();
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        transform.x = centerX - (centerX - transform.x) * (newScale / transform.scale);
        transform.y = centerY - (centerY - transform.y) * (newScale / transform.scale);
        transform.scale = newScale;
        
        updateTransform();
    }

    function centerMap() {
        // Hardcoded initial center (Can be calculated based on nodes bounding box in prod)
        transform = { x: 50, y: 50, scale: 0.9 };
        updateTransform();
    }

    // ==========================================
    // 5. Interactions (Click Node & Update UI)
    // ==========================================
    async function handleNodeClick(nodeId, nodeEl) {
        // Visual Selection
        document.querySelectorAll('.skill-node').forEach(n => n.classList.remove('selected'));
        nodeEl.classList.add('selected');
        
        // Open Panel
        detailPanel.classList.remove('d-none');
        detailPanel.classList.add('d-flex');
        
        try {
            const res = await fetch('ajax_skill_map.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_skill_details', skill_id: nodeId })
            });
            const data = await res.json();
            
            if(data.status === 'success') {
                populateLeftPanel(data.data);
            }
        } catch (error) {
            console.error(error);
        }
    }

    function populateLeftPanel(data) {
        document.getElementById('sdCategory').innerText = data.category;
        document.getElementById('sdTitle').innerText = data.label;
        document.getElementById('sdDesc').innerText = data.description;
        
        document.getElementById('sdLevelStr').innerText = data.levelString;
        document.getElementById('sdProgressTxt').innerText = `${data.level}%`;
        document.getElementById('sdProgressBar').style.width = `${data.level}%`;
        
        // Stars
        let starsHtml = '';
        for(let i=1; i<=5; i++) {
            starsHtml += `<i class="bi bi-star${i <= data.demand ? '-fill' : ''}"></i> `;
        }
        document.getElementById('sdMarketDemand').innerHTML = starsHtml;
        
        // Prerequisites
        let reqHtml = '';
        if(data.preReqDetails && data.preReqDetails.length > 0) {
            data.preReqDetails.forEach(req => {
                const color = req.status === 'mastered' ? 'success' : 'secondary';
                reqHtml += `<span class="badge bg-${color} bg-opacity-25 border border-${color} text-${color}"><i class="bi bi-${req.status === 'mastered' ? 'check' : 'clock'}"></i> ${req.label}</span>`;
            });
        } else {
            reqHtml = '<span class="text-muted fs-sm">None (Fundamental)</span>';
        }
        document.getElementById('sdPreReq').innerHTML = reqHtml;
        
        // Button Logic
        const btn = document.getElementById('sdActionBtn');
        if(data.status === 'locked') {
            btn.className = 'btn btn-secondary w-100 rounded-pill py-2 fw-bold';
            btn.innerHTML = '<i class="bi bi-lock"></i> Locked';
            btn.disabled = true;
        } else if(data.status === 'mastered') {
            btn.className = 'btn btn-outline-success w-100 rounded-pill py-2 fw-bold';
            btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Review Topic';
            btn.disabled = false;
        } else {
            btn.className = 'btn btn-primary w-100 rounded-pill py-2 fw-bold glow';
            btn.innerHTML = 'Continue Learning';
            btn.disabled = false;
        }
    }
    
    function updateAiInsights(data) {
        document.getElementById('careerMatchTxt').innerText = `${data.matchPercentage}%`;
        document.getElementById('careerMatchRing').setAttribute('stroke-dasharray', `${data.matchPercentage}, 100`);
        document.getElementById('aiCareerTitle').innerText = careerSelector.options[careerSelector.selectedIndex].text;
        document.getElementById('aiPredictionTxt').innerHTML = data.prediction;
        
        // Missing Skills list
        const list = document.getElementById('missingSkillsList');
        list.innerHTML = '';
        data.missing.forEach(skill => {
            const isCritical = skill.importance === 'Critical' ? 'critical' : '';
            list.innerHTML += `
                <div class="missing-skill-item ${isCritical} d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold fs-sm text-white">${skill.label}</div>
                        <div class="fs-xs text-muted">Priority: <span class="${skill.importance === 'Critical' ? 'text-danger' : 'text-warning'}">${skill.importance}</span></div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary rounded-circle" title="View Detail"><i class="bi bi-chevron-right"></i></button>
                </div>
            `;
        });
    }

    return { init };
})();

// Start App
document.addEventListener('DOMContentLoaded', skillMapApp.init);