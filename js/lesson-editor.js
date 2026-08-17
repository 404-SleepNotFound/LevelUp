$(document).ready(function() {
    const API_ENDPOINT = '/api/ajax_lesson_editor.php';

    // Cache jQuery selectors
    const $courseSelect = $('#course_id_select');
    const $chapterSelect = $('#chapter_id_select');
    const $lessonSelect = $('#lesson_id_select');
    const $lessonContentWrapper = $('#lesson-content-wrapper');
    const $editorModeBadge = $('#editor-mode-badge');
    const $lessonIdInput = $('#lesson_id_input');
    const $form = $('#lessonMasterForm');

    // --- EVENT HANDLERS ---

    // 1. Course selection changes
    $courseSelect.on('change', function() {
        const courseId = $(this).val();
        resetUI(true, true); // Reset chapters and lessons

        if (courseId) {
            fetchChapters(courseId);
            $chapterSelect.prop('disabled', false).trigger('lu:update');
        } else {
            $chapterSelect.prop('disabled', true).html('<option value="">-- กรุณาเลือกคอร์สก่อน --</option>').trigger('lu:update');
        }
    });

    // 2. Chapter selection changes
    $chapterSelect.on('change', function() {
        const chapterId = $(this).val();
        resetUI(false, true); // Reset lessons only

        if (chapterId) {
            fetchLessons(chapterId);
            $lessonSelect.prop('disabled', false).trigger('lu:update');
        } else {
            $lessonSelect.prop('disabled', true).html('<option value="">-- กรุณาเลือก Chapter ก่อน --</option>').trigger('lu:update');
        }
    });

    // 3. Lesson selection changes
    $lessonSelect.on('change', function() {
        const lessonId = $(this).val();
        const lessonType = $(this).find('option:selected').data('lesson-type');
        
        if (lessonId) {
            $lessonIdInput.val(lessonId);
            fetchLessonDetails(lessonId);
            toggleEditorTools(lessonType);
            $lessonContentWrapper.slideDown(300);
            $editorModeBadge.html('<i class="bi bi-pencil-fill me-1"></i> Mode: Editing').removeClass('bg-secondary').addClass('bg-info');
        } else {
            resetUI(false, false);
        }
    });
    
    // Also attach to the type selector inside the form
    $form.on('change', '#lesson_type_select', function() {
        toggleEditorTools($(this).val());
    });


    // --- AJAX FUNCTIONS ---

    function fetchChapters(courseId) {
        makeAjaxRequest({ action: 'get_chapters', course_id: courseId }, function(data) {
            populateSelect($chapterSelect, data.chapters, 'id', 'title', '-- เลือก Chapter --');
        });
    }

    function fetchLessons(chapterId) {
        makeAjaxRequest({ action: 'get_lessons', chapter_id: chapterId }, function(data) {
            populateSelect($lessonSelect, data.lessons, 'id', 'title', '-- เลือกบทเรียน --', 'lesson_type');
        });
    }

    function fetchLessonDetails(lessonId) {
        makeAjaxRequest({ action: 'get_lesson_details', lesson_id: lessonId }, function(data) {
            populateForm(data);
        });
    }

    function makeAjaxRequest(params, successCallback) {
        $.ajax({
            url: API_ENDPOINT,
            type: 'POST',
            data: params,
            dataType: 'json',
            beforeSend: function() {
                // You can add a loading indicator here if needed
            },
            success: function(response) {
                if (response && response.status === 'success') {
                    successCallback(response);
                } else {
                    const errorMsg = response.message || 'Unknown API error';
                    console.error('API Error:', errorMsg);
                    // Optionally use a more user-friendly alert
                    // LevelUpAlert.error('API Error', errorMsg);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                // LevelUpAlert.ajaxError(xhr, status, error);
            },
            complete: function() {
                // Hide loading indicator
            }
        });
    }

    // --- UI HELPER FUNCTIONS ---

    function populateSelect($select, items, valueKey, textKey, defaultOption, dataKey = null) {
        let options = '';
        if (items && items.length > 0) {
            options = `<option value="">${defaultOption}</option>`;
            items.forEach(item => {
                const dataAttribute = dataKey ? `data-${dataKey}="${item[dataKey]}"` : '';
                options += `<option value="${item[valueKey]}" ${dataAttribute}>${item[textKey]}</option>`;
            });
        } else {
            const selectId = $select.attr('id');
            let noDataMessage = '-- ไม่มีข้อมูล --';
            if (selectId === 'chapter_id_select') {
                noDataMessage = '-- ไม่มี Chapter ในคอร์สนี้ (กรุณาไปสร้างก่อน) --';
            } else if (selectId === 'lesson_id_select') {
                noDataMessage = '-- ไม่มี Lesson ใน Chapter นี้ --';
            }
            options = `<option value="">${noDataMessage}</option>`;
        }
        $select.html(options).trigger('lu:update'); // lu:update is for the custom dropdown
    }
    
    function populateForm(data) {
        const lesson = data.lesson;
        if (!lesson) return;

        $form.find('[name="title"]').val(lesson.title);
        $form.find('[name="subtitle"]').val(lesson.subtitle);
        $form.find('[name="lesson_order"]').val(lesson.lesson_order);
        $form.find('[name="status"]').val(lesson.status).trigger('lu:update');
        $form.find('[name="lesson_type"]').val(lesson.lesson_type).trigger('lu:update');
        
        // Populate tool-specific fields
        $form.find('[name="content"]').val(lesson.content);
        $form.find('[name="video_url"]').val(lesson.video_url);
        
        // ... populate other fields as needed ...
        
        // IMPORTANT: Also update the hidden course_id and chapter_id in the main form
        $form.find('[name="course_id"]').val(lesson.course_id).trigger('lu:update');
        $form.find('[name="chapter_id"]').val(lesson.chapter_id).trigger('lu:update');
    }
    
    function toggleEditorTools(type) {
        const container = $('#editor-tools-container');
        const prompt = $('#editor-prompt');
        const modules = $('.tool-module');

        modules.hide(); // Hide all

        if (!type) {
            prompt.slideDown();
            container.slideUp();
            return;
        }

        prompt.hide();
        container.show();

        // Show the correct module
        switch (type) {
            case 'theory':
            case 'video':
                $('#tool-module-theory').slideDown();
                break;
            case 'slide':
                $('#tool-module-slide').slideDown();
                break;
            case 'quiz':
                $('#tool-module-quiz').slideDown();
                break;
            case 'lab':
                $('#tool-module-lab').slideDown();
                break;
        }
    }

    function resetUI(resetChapters, resetLessons) {
        $lessonContentWrapper.slideUp(200);
        $form[0].reset();
        $lessonIdInput.val('');
        $editorModeBadge.html('<i class="bi bi-gear me-1"></i> Mode: Standby').removeClass('bg-info').addClass('bg-secondary');

        if (resetChapters) {
             $chapterSelect.prop('disabled', true).html('<option value="">-- กรุณาเลือกคอร์สก่อน --</option>').trigger('lu:update');
        }
        if (resetLessons) {
            $lessonSelect.prop('disabled', true).html('<option value="">-- กรุณาเลือก Chapter ก่อน --</option>').trigger('lu:update');
        }
        toggleEditorTools(null);
    }
    
    // --- INITIALIZATION ---
    resetUI(true, true); // Initial state

    // Check if a course is already selected on page load and trigger the change event to load chapters.
    const initialCourseId = $courseSelect.val();
    if (initialCourseId) {
        $courseSelect.trigger('change');
    }
});
