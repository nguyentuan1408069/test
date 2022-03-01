$(document).ready(function () {
    let divTaskCompleted = $('.finished');
    let allDivTask = $('.task');
    let sortByName = 'asc';
    let sortByPriority = 'asc';

    $('.all-tasks').append(allDivTask.length);
    $('.completed-tasks').append(divTaskCompleted.length);

    $('.addBtn').on('click', function () {
        $('.modal-title').html('Add task');
        $('#save').show();
        $('#edit').addClass('hidden');
        $('#name').val('');
        $('#priority').val(3);
        $('.statusDiv').addClass('hidden');
    });

    $('#save').on('click', function () {
        let name = $('#name').val();
        let priority = $('#priority').val();
        $.ajax({
            type: "POST",
            url: "tasks/create",
            data: {
                name: name,
                priority: priority
            },
            async: true,
            cache: false,
            error: function () {
                alert('Something was wrong, please try again')
            },
            success: function () {
                location.href = '/tasks';
            }
        });
    })

    $('.finishBtn').on('click', function () {
        let taskId = $(this).data('id');
        $.ajax({
            type: "POST",
            url: "tasks/finish",
            data: {
                taskId: taskId
            },
            async: true,
            cache: false,
            error: function () {
                alert('Something was wrong, please try again')
            },
            success: function () {
                location.href = '/tasks';
            }
        });
    })

    $('.viewBtn').on('click', function () {
        let taskId = $(this).data('id');
        let name = $(this).data('name');
        let priority = $(this).data('priority');
        let status = $(this).data('status');
        $('#view-name').val(name);
        $('#view-priority').val(priority);
        $('#view-status').val(status);
        $('#view-taskId').val(taskId);
        $('#view').modal('show');
        $('.statusDiv').removeClass('hidden');
    })

    $('.updateBtn').on('click', function () {
        let taskId = $(this).data('id');
        let name = $(this).data('name');
        let priority = $(this).data('priority');
        let status = $(this).data('status');
        $('#name').val(name);
        $('#priority').val(priority);
        $('#status').val(status);
        $('#taskId').val(taskId);
        $('#myModal').modal('show');
        $('.modal-title').html('Edit task');
        $('#save').hide();
        $('#edit').removeClass('hidden');
        $('.statusDiv').removeClass('hidden');
    })

    $('#edit').on('click', function () {
        let taskId = $('#taskId').val();
        let name = $('#name').val();
        let priority = $('#priority').val();
        let status = $('#status').val();
        $.ajax({
            type: "POST",
            url: "tasks/update",
            data: {
                taskId: taskId,
                name: name,
                priority: priority,
                status: status
            },
            async: true,
            cache: false,
            error: function () {
                alert('Something was wrong, please try again')
            },
            success: function () {
                location.href = '/tasks';
            }
        });
    });

    $('.deleteBtn').on('click', function () {
        let taskId = $(this).data('id');
        if (confirm('Are you sure?')) {
            $.ajax({
                type: "POST",
                url: "tasks/delete",
                data: {
                    taskId: taskId
                },
                async: true,
                cache: false,
                error: function () {
                    alert('Something was wrong, please try again')
                },
                success: function () {
                    location.href = '/tasks';
                }
            });
        }
    })

    $('.sortByName').on('click', function () {
        $(this).parent().find('.sortByName >i').removeClass('hidden');
        sortDir = 'desc';

        if ($(this).data('sort') !== 'asc') {
            sortDir = 'asc';
        }
        sortByName = sortDir;
        $(this).data('sort', sortDir).find('.fa').attr('class', 'fa fa-sort-' + sortDir);
        getData(sortByName, sortByPriority)

    })

    $('.sortByPriority').on('click', function () {
        $(this).parent().find('.sortByPriority >i').removeClass('hidden');
        sortDir = 'desc';

        if ($(this).data('sort') !== 'asc') {
            sortDir = 'asc';
        }
        sortByPriority = sortDir;
        $(this).data('sort', sortDir).find('.fa').attr('class', 'fa fa-sort-' + sortDir);
        getData(sortByName, sortByPriority);
    })

    function getData(sortByName, sortByPriority) {
        $.ajax({
            type: "GET",
            url: "tasks",
            data: {
                sortByName: sortByName,
                sortByPriority: sortByPriority
            },
            async: true,
            cache: false,
            error: function () {
                alert('Something was wrong, please try again')
            },
            success: function (data) {
                $('.data').hide();
                data = data.data;
                let tasks = data.tasks;
                let priorityTask = data.tasks[0]['priority'];
                let check = true;
                let html = '';
                tasks.forEach(task => {
                    if (priorityTask != task['priority']) {
                        check = true;
                        priorityTask = task['priority'];
                    }
                    if (priorityTask == task['priority'] && check) {
                        check = false;
                        html += `<div class="listPriority">
                                Priority:
                                ${data.priority[priorityTask]}
                            </div>`;
                    }
                    let classP = 'detail';
                    let hidden = '';
                    if (task['status'] == 1) {
                        classP = 'finished'
                        hidden = 'hidden';
                    }

                    html += `<div class="task">
                            <p class="${classP}">${task['name']}</p>
                            <div class="actions">
                                <i data-id="${task['id']}" data-name="${task['name']}"
                                   data-priority="${task['priority']}"
                                   data-status="${task['status']}" class="fas fa-eye viewBtn"></i>
                                <i data-id="${task['id']}" data-name="<?php echo $task['name'] ?>"
                                   data-priority="${task['priority']}"
                                   data-status="${task['status']}" class="fas fa-edit updateBtn"></i>
                                <i data-id="${task['id']}"
                                   class="fas fa-check finishBtn ${hidden}"></i>
                                <i data-id="${task['id']}" class="fas fa-trash-alt deleteBtn"></i>
                            </div>
                        </div>`;

                })
                $('.dataSearch').html(html);
            }
        });
    }
})