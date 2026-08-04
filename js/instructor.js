/**
 * LevelUp Core Operations Script - Instructor Suite Core Engine
 */
$(document).ready(function() {
    
    // 1. เรียกทำกระบวนการการโหลดสเตตข้อมูลแบบ Asynchronous ทันทีเมื่อ DOM พร้อม
    loadDashboardMetrics();
    loadActivityFeed();
    loadInstructorCourses();

    // 2. ดึงข้อมูลตัวเลขกล่อง Dashboard Statistics
    function loadDashboardMetrics() {
        $.ajax({
            url: 'ajax_instructor.php?action=fetch_stats',
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    var domHtml = '';
                    res.payload.forEach(function(item) {
                        domHtml += `
                            <div class="col-6 col-md-3">
                                <div class="dashboard-card p-3 d-flex align-items-center gap-3">
                                    <div class="stat-icon ${item.variant}"><i class="bi ${item.icon}"></i></div>
                                    <div>
                                        <p class="mb-0 text-muted opacity-75" style="font-size:0.75rem;">${item.title}</p>
                                        <h5 class="fw-bold mb-0 text-white mt-0.5" style="font-size:1.15rem;">${item.value}</h5>
                                    </div>
                                </div>
                            </div>`;
                    });
                    $('#stats-dynamic-container').html(domHtml);
                }
            }
        });
    }

    // 3. ดึงฟีดข้อมูลประวัติกิจกรรมผู้เรียนล่าสุด
    function loadActivityFeed() {
        $.ajax({
            url: 'ajax_instructor.php?action=fetch_activities',
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    var domHtml = '';
                    res.payload.forEach(function(act) {
                        domHtml += `
                            <div class="d-flex gap-3 mb-3">
                                <div class="${act.color} rounded-circle d-flex align-items-center justify-content-center" style="width:30px; height:30px; flex-shrink:0; font-size:0.85rem;"><i class="bi ${act.icon}"></i></div>
                                <div>
                                    <p class="mb-0 text-white fw-semibold" style="font-size:0.8rem;">${act.message}</p>
                                    <span class="text-muted" style="font-size:0.7rem;">${act.time}</span>
                                </div>
                            </div>`;
                    });
                    $('#activities-dynamic-container').html(domHtml);
                }
            }
        });
    }

    // 4. ดึงตารางรายการคอร์สทั้งหมดและจัดการการแสดงผล
    function loadInstructorCourses() {
        $.ajax({
            url: 'ajax_instructor.php?action=fetch_courses',
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    renderTableRows(res.payload);
                }
            }
        });
    }

    function renderTableRows(dataset) {
        var domHtml = '';
        if (dataset.length === 0) {
            $('#courses-dynamic-tbody').html('');
            $('#courses-empty-alert').removeClass('d-none');
            return;
        }
        $('#courses-empty-alert').addClass('d-none');
        
        dataset.forEach(function(row) {
            var statusBadge = row.status === 'Published' ? 'bg-success bg-opacity-15 text-success border border-success border-opacity-20' : 'bg-secondary bg-opacity-20 text-light';
            domHtml += `
                <tr data-search-key="${row.title.toLowerCase()}">
                    <td class="fw-bold text-white">${row.title}</td>
                    <td><span class="badge bg-dark border border-secondary border-opacity-25 text-muted">${row.category}</span></td>
                    <td>${row.students.toLocaleString()} คน</td>
                    <td class="text-info fw-semibold">${row.earnings}</td>
                    <td><i class="bi bi-star-fill text-warning me-1"></i>${row.rating}</td>
                    <td><span class="badge ${statusBadge}">${row.status}</span></td>
                    <td class="text-end">
                        <button type="button" class="btn btn-outline-surface btn-sm px-2 text-light" title="แก้ไขโครงสร้าง"><i class="bi bi-pencil-square"></i></button>
                    </td>
                </tr>`;
        });
        $('#courses-dynamic-tbody').html(domHtml);
    }

    // 5. ระบบกรองชื่อคอร์สเรียลไทม์ฝั่งไคลเอนต์ (Client-side Live Search Filter)
    $('#courseLiveSearch').on('keyup', function() {
        var keyword = $(this).val().toLowerCase();
        var tableRows = $('#courses-dynamic-tbody tr');
        var matchCounter = 0;

        tableRows.each(function() {
            var isMatched = $(this).data('search-key').indexOf(keyword) > -1;
            $(this).toggle(isMatched);
            if (isMatched) matchCounter++;
        });

        if (matchCounter === 0 && tableRows.length > 0) {
            $('#courses-empty-alert').removeClass('d-none');
        } else {
            $('#courses-empty-alert').addClass('d-none');
        }
    });

    // 6. กลไกเครื่องมือควบคุมอินเทอร์เฟซแบบสเต็ปเปอร์ (Stepper Form Validation Engine)
    var stepIndex = 1;
    var maxSteps = 3;

    function transitionStepUI() {
        $('.step-panel').removeClass('active');
        $(`.step-panel[data-step="${stepIndex}"]`).addClass('active');

        $('.step-node').removeClass('active');
        for (var i = 1; i <= stepIndex; i++) {
            $(`.step-node[data-step="${i}"]`).addClass('active');
        }

        // การควบคุมการป้อนปุ่มย้อนกลับและถัดไป
        $('#stepBackwardBtn').toggleClass('d-none', stepIndex === 1);
        if (stepIndex === maxSteps) {
            $('#stepForwardBtn').addClass('d-none');
            $('#stepSubmitFormBtn').removeClass('d-none');
        } else {
            $('#stepForwardBtn').removeClass('d-none');
            $('#stepSubmitFormBtn').addClass('d-none');
        }
    }

    $('#stepForwardBtn').on('click', function() {
        var currentPaneValid = true;
        $(`.step-panel[data-step="${stepIndex}"] [required]`).each(function() {
            if (!this.checkValidity()) {
                currentPaneValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (currentPaneValid) {
            if (stepIndex < maxSteps) {
                stepIndex++;
                transitionStepUI();
            }
        }
    });

    $('#stepBackwardBtn').on('click', function() {
        if (stepIndex > 1) {
            stepIndex--;
            transitionStepUI();
        }
    });

    // เพิ่มโครงสร้างอินพุตบทเรียนย่อยแบบ Dynamic
    $('#appendChapterButton').on('click', function() {
        var indexNum = $('#dynamic-chapter-list .input-group').length + 1;
        var elementHtml = `
            <div class="input-group">
                <span class="input-group-text bg-secondary bg-opacity-20 border border-secondary border-opacity-25 text-white fs-xs" style="min-width: 80px;">บทที่ ${indexNum}</span>
                <input type="text" name="chapters[]" class="form-control text-white bg-dark bg-opacity-40 border border-secondary border-opacity-25 shadow-none fs-sm" placeholder="ระบุชื่อหัวข้อบทเรียนหรือโครงงานถัดไป..." required>
            </div>`;
        $('#dynamic-chapter-list').append(elementHtml);
    });

    // 7. จัดการ Submit ข้อมูลแบบไร้รอยต่อผ่าน AJAX (AJAX Submission Pipeline)
    $('#courseBuilderForm').on('submit', function(event) {
        event.preventDefault();
        $('#validation-error-box').addClass('d-none');

        var formElement = this;
        if (!formElement.checkValidity()) {
            $('#validation-error-box').removeClass('d-none');
            return;
        }

        var packetSerialized = $(this).serialize();

        $.ajax({
            url: 'ajax_instructor.php?action=execute_creation',
            method: 'POST',
            data: packetSerialized,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    // ปิดหน้าต่างโมดอล รีเซ็ตแบบฟอร์มเพื่อเตรียมรับข้อมูลครั้งถัดไป
                    $('#createCourseModal').modal('hide');
                    formElement.reset();
                    stepIndex = 1;
                    transitionStepUI();

                    // เรียกข้อมูลคอร์สเรียนขึ้นมาวาด DOM ใหม่ทันทีแบบ Single Page Application (SPA)
                    loadInstructorCourses();
                } else {
                    alert(res.message);
                }
            },
            error: function() {
                alert('เกิดข้อผิดพลาดด้านเครือข่ายความปลอดภัย กรุณาเรียกใหม่อีกครั้ง');
            }
        });
    });
});