// ==========================================
// 🎯 CUSTOM DROPDOWN ENGINE (FIXED & IMPROVED)
// ==========================================
$(document).off('click', '.custom-dropdown-btn').on('click', '.custom-dropdown-btn', function(e) {
    e.stopPropagation();
    var $wrapper = $(this).closest('.custom-dropdown-wrapper');
    $('.custom-dropdown-wrapper').not($wrapper).removeClass('active');
    $wrapper.toggleClass('active');
});

$(document).off('click', '.custom-dropdown-item').on('click', '.custom-dropdown-item', function(e) {
    e.stopPropagation();
    var $item = $(this);
    var $wrapper = $item.closest('.custom-dropdown-wrapper');
    var val = $item.data('value');
    var text = $.trim($item.text());

    // อัปเดตข้อความบนปุ่ม
    $wrapper.find('.selected-text').text(text);
    
    // อัปเดตค่าใน Hidden Input และยิง Trigger Change
    var $hiddenInput = $wrapper.find('input[type="hidden"]');
    $hiddenInput.val(val).trigger('change');

    // สลับ Class Active/Selected
    $wrapper.find('.custom-dropdown-item').removeClass('selected');
    $item.addClass('selected');
    $wrapper.removeClass('active');
    $wrapper.find('.custom-dropdown-btn').removeClass('border-danger');
});

// ปิด Dropdown เมื่อคลิกนอกพื้นที่
$(document).off('click.dropdownClose').on('click.dropdownClose', function(e) {
    if (!$(e.target).closest('.custom-dropdown-wrapper').length) {
        $('.custom-dropdown-wrapper').removeClass('active');
    }
});

// Event Triggers เมื่อค่าใน Dropdown เปลี่ยนแปลง
$(document).off('change', '#course_difficulty').on('change', '#course_difficulty', function() {
    updateDifficultyHelp();
});

$(document).off('change', '#course_type').on('change', '#course_type', function() {
    togglePriceFields($(this).val());
});
