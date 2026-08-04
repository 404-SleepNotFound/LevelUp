$(document).ready(function () {

    /**
     * 🎨 ฟังก์ชันดึงค่าการแต่งสี SweetAlert2 โดยอิงจาก data-theme บน Header
     */
    function getSwalThemeConfig() {
        // ดึงค่าธีมปัจจุบันจาก <html> ที่ถูกควบคุมโดยปุ่มบน Header
        const currentTheme = $('html').attr('data-theme') || localStorage.getItem('theme') || 'dark';
        const isLight = currentTheme === 'light';

        return {
            background: isLight ? '#ffffff' : '#0f172a',
            color: isLight ? '#0f172a' : '#f8fafc',
            border: isLight ? '1px solid #e2e8f0' : '1px solid #1e293b'
        };
    }

    // จัดการการส่งฟอร์ม Profile
    $('#profileForm').on('submit', function (e) {
        e.preventDefault();

        var formData = {};
        $(this).serializeArray().forEach(function (item) {
            formData[item.name] = item.value;
        });

        // ดึงค่า user_id จาก Attribute data-user-id ของ Form
        var userId = $(this).data('user-id');

        var requestData = {
            action: 'update_profile',
            data: formData,
            user_id: userId
        };

        $.ajax({
            url: 'services/user.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
            dataType: 'JSON',
            success: function (response) {
                // ดึงการตั้งค่าสีตามธีมล่าสุดจาก Header
                var theme = getSwalThemeConfig();

                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกข้อมูลสำเร็จ',
                        text: response.message || 'ข้อมูลของคุณถูกบันทึกเรียบร้อยแล้ว',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#10b981',
                        background: theme.background,
                        color: theme.color,
                        customClass: {
                            popup: 'swal2-border-custom'
                        },
                        willOpen: (toast) => {
                            toast.style.border = theme.border;
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.message || 'ไม่สามารถบันทึกข้อมูลได้',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#ef4444',
                        background: theme.background,
                        color: theme.color,
                        willOpen: (toast) => {
                            toast.style.border = theme.border;
                        }
                    });
                }
            },
            error: function (error) {
                var theme = getSwalThemeConfig();

                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#ef4444',
                    background: theme.background,
                    color: theme.color,
                    willOpen: (toast) => {
                        toast.style.border = theme.border;
                    }
                });
            }
        });
    });
});