/* ======================================================
   LevelUp Global Custom Dropdown Enhancer
   ------------------------------------------------------
   แปลง <select class="lu-select"> ทุกตัวในหน้าเว็บให้กลายเป็น
   custom dropdown (div) ที่ใช้ดีไซน์จาก dropdown-select.css
   โดยอัตโนมัติ ไม่ว่า select นั้นจะ:
     - อยู่ในหน้าตั้งแต่โหลดครั้งแรก
     - ถูก inject เข้ามาทีหลังผ่าน AJAX (เช่น instructor.php
       โหลด create-course.php เข้า #studio-content-placeholder)
     - ถูกสร้างขึ้นด้วย JS ระหว่างทาง (dynamic form)

   วิธีใช้: แค่โหลดไฟล์นี้ไว้หน้าไหนก็ได้ที่มี
   <select class="lu-select"> ไม่ต้องเขียน JS enhancer เองอีก
       <script src="js/lu-dropdown.js" defer></script>

   ไม่มี dependency กับ jQuery หรือ library อื่นใดทั้งสิ้น
   ====================================================== */
(function () {
  'use strict';

  var ENHANCED_FLAG = 'luDropdownEnhanced';

  // ฟังก์ชันช่วยตั้งค่า Label (แสดงผลทั้ง ไอคอน และ ข้อความ)
  function updateLabel(labelElement, opt) {
    labelElement.innerHTML = '';
    if (opt && opt.dataset && opt.dataset.icon) {
      var labelIcon = document.createElement('i');
      labelIcon.className = 'bi ' + opt.dataset.icon + ' me-2 text-info';
      labelElement.appendChild(labelIcon);
    }
    var textNode = document.createTextNode(opt ? opt.textContent : '');
    labelElement.appendChild(textNode);
  }

  // New function to render or re-render the dropdown items
  function renderDropdownItems(select, menu, labelElement, closeMenuFn) {
    // Clear existing items
    menu.innerHTML = '';

    var options = select.querySelectorAll('option');
    var items = []; // To store newly created custom-dropdown-item elements for event binding

    options.forEach(function (opt, index) {
      var item = document.createElement('div');
      item.className = 'custom-dropdown-item';
      item.dataset.value = opt.value;
      item.setAttribute('role', 'option');

      // ตรวจสอบและเพิ่ม Icon ประจำ Item (ถ้ามี data-icon)
      if (opt.dataset.icon) {
        var icon = document.createElement('i');
        icon.className = 'bi ' + opt.dataset.icon + ' me-2 text-info';
        item.appendChild(icon);
      }

      var textSpan = document.createTextNode(opt.textContent);
      item.appendChild(textSpan);

      if (opt.disabled) item.classList.add('disabled-item');
      if (opt.selected) {
        item.classList.add('selected');
      }

      menu.appendChild(item);
      items.push(item);

      // Attach click listener for new item
      item.addEventListener('click', function (e) {
        e.stopPropagation();
        if (item.classList.contains('disabled-item')) return;

        // Remove 'selected' from all items and add to the clicked one
        items.forEach(function (i) { i.classList.remove('selected'); });
        item.classList.add('selected');

        select.value = item.dataset.value; // Update the original select's value
        select.classList.remove('is-invalid');
        // Dispatch native 'change' event
        select.dispatchEvent(new Event('change', { bubbles: true }));

        // Update the main button label from the selected option
        var selectedOpt = Array.from(options).find(o => o.value === select.value);
        if (selectedOpt) {
          updateLabel(labelElement, selectedOpt);
        }

        if (closeMenuFn) closeMenuFn();
      });
    });

    // Update the main button label based on the current select.value
    var currentlySelectedOpt = Array.from(options).find(o => o.value === select.value && o.selected) ||
                               Array.from(options).find(o => o.value === select.value);
    if (currentlySelectedOpt) {
      updateLabel(labelElement, currentlySelectedOpt);
    } else if (options.length > 0) {
      // If no option is selected in the original select, try to select the first non-disabled one
      var firstNonDisabled = Array.from(options).find(o => !o.disabled);
      if (firstNonDisabled) {
        select.value = firstNonDisabled.value;
        firstNonDisabled.selected = true;
        updateLabel(labelElement, firstNonDisabled);
        // Mark the corresponding custom item as selected
        var firstCustomItem = items.find(i => i.dataset.value === firstNonDisabled.value);
        if (firstCustomItem) {
          firstCustomItem.classList.add('selected');
        }
      } else {
        // All options are disabled or no options, clear label
        updateLabel(labelElement, null);
      }
    } else {
      // No options at all, clear label
      updateLabel(labelElement, null);
    }
  }

  function initDropdownWrapper(select) {
    if (select.dataset[ENHANCED_FLAG] === '1') return;
    select.dataset[ENHANCED_FLAG] = '1';

    var wrapper = document.createElement('div');
    wrapper.className = 'custom-dropdown-wrapper';

    var btn = document.createElement('div');
    btn.className = 'custom-dropdown-btn';
    btn.setAttribute('tabindex', '0');
    btn.setAttribute('role', 'button');
    btn.setAttribute('aria-haspopup', 'listbox');
    btn.setAttribute('aria-expanded', 'false');

    var label = document.createElement('span');
    label.className = 'custom-dropdown-label';
    btn.appendChild(label);

    var menu = document.createElement('div');
    menu.className = 'custom-dropdown-menu';
    menu.setAttribute('role', 'listbox');

    // ย้าย select จริงเข้ามาไว้ในกล่องเดียวกันแล้วซ่อนไว้
    // (ยังอยู่ใน <form> เดิม name/id/required เดิมทุกอย่าง เพื่อ submit ได้ปกติ)
    select.parentNode.insertBefore(wrapper, select);
    wrapper.appendChild(btn);
    wrapper.appendChild(menu);
    wrapper.appendChild(select);
    select.style.display = 'none';

    function closeMenu() {
      wrapper.classList.remove('active');
      btn.setAttribute('aria-expanded', 'false');
    }

    function toggleMenu() {
      var willOpen = !wrapper.classList.contains('active');
      document.querySelectorAll('.custom-dropdown-wrapper.active').forEach(function (w) {
        if (w !== wrapper) w.classList.remove('active');
      });
      wrapper.classList.toggle('active', willOpen);
      btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
    }

    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      toggleMenu();
    });

    btn.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        toggleMenu();
      } else if (e.key === 'Escape') {
        closeMenu();
      }
    });

    // Initial render
    renderDropdownItems(select, menu, label, closeMenu);

    // MutationObserver to watch for changes in the <select> element's options
    var observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.type === 'childList' && mutation.target === select) {
          renderDropdownItems(select, menu, label, closeMenu);
        }
      });
    });
    observer.observe(select, { childList: true });
  }

  function enhanceAll(scope) {
    (scope || document).querySelectorAll('select.lu-select').forEach(initDropdownWrapper);
  }

  // ปิดเมนูเมื่อคลิกข้างนอก (ผูก event ครั้งเดียว ใช้ delegation)
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.custom-dropdown-wrapper')) {
      document.querySelectorAll('.custom-dropdown-wrapper.active').forEach(function (w) {
        w.classList.remove('active');
      });
    }
  });

  // สแกนตั้งแต่ตอนโหลดหน้าแรก
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { enhanceAll(document); });
  } else {
    enhanceAll(document);
  }

  // คอยเฝ้าดู DOM ตลอด - ถ้ามี select.lu-select โผล่มาใหม่
  // (เช่นถูก inject มาจาก AJAX) จะถูกแปลงให้อัตโนมัติทันที
  // ไม่ต้องเรียกฟังก์ชันเองหลัง .html(response) อีกต่อไป
  var observer = new MutationObserver(function (mutations) {
    mutations.forEach(function (m) {
      m.addedNodes.forEach(function (node) {
        if (node.nodeType !== 1) return;
        if (node.matches && node.matches('select.lu-select')) {
          initDropdownWrapper(node); // Call initDropdownWrapper for new select elements
        } else if (node.querySelectorAll) {
          enhanceAll(node);
        }
      });
    });
  });
  observer.observe(document.documentElement, { childList: true, subtree: true });

  // เผื่อหน้าไหนอยากเรียกเองแบบเจาะจง scope (optional)
  window.LuDropdown = { enhance: enhanceAll };
})();