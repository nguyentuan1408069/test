<!DOCTYPE html>
<html>
<head>
    <title>To Do</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/js/main.js"></script>
</head>
<body>

<div class="container">
    <div class="todo">
        <div class="add-task">
            <button type="button" class="addTask" data-toggle="modal" data-target="#myModal">
                <i class="fas fa-plus addBtn"></i>
            </button>
        </div>
        <div class="controls">
            <div>Sort by:</div>
            <a class="sortByName" href="#" data-sortBy="name" data-sort="asc">Name &nbsp;<i class="fa fa-sort-asc hidden"></i></a>
            <a class="sortByPriority" href="#" data-sortBy="priority" data-sort="asc">Priority &nbsp;<i class="fa fa-sort-asc hidden"></i></a>
        </div>
        <div class="list-tasks">
            <div class="data">
                <?php if ($tasks['pagination']['total'] > 0): ?>
                    <?php
                    $priorityTask = $tasks['tasks'][0]['priority'];
                    $check = true;
                    ?>
                    <?php foreach ($tasks['tasks'] as $task): ?>
                        <?php if ($priorityTask != $task['priority']) {
                            $check = true;
                            $priorityTask = $task['priority'];
                        } ?>
                        <?php if ($priorityTask == $task['priority'] && $check): ?>
                            <div class="listPriority">
                                Priority:
                                <?php
                                $check = false;
                                echo $priority[$priorityTask];
                                ?>
                            </div>
                        <?php endif; ?>
                        <div class="task">
                            <p class="<?php echo $task['status'] == 1 ? 'finished' : 'detail' ?>"><?php echo $task['name'] ?></p>
                            <div class="actions">
                                <i data-id="<?php echo $task['id'] ?>" data-name="<?php echo $task['name'] ?>"
                                   data-priority="<?php echo $task['priority'] ?>"
                                   data-status="<?php echo $task['status'] ?>" class="fas fa-eye viewBtn"></i>
                                <i data-id="<?php echo $task['id'] ?>" data-name="<?php echo $task['name'] ?>"
                                   data-priority="<?php echo $task['priority'] ?>"
                                   data-status="<?php echo $task['status'] ?>" class="fas fa-edit updateBtn"></i>
                                <i data-id="<?php echo $task['id'] ?>"
                                   class="fas fa-check finishBtn <?php echo $task['status'] == 1 ? 'hidden' : '' ?>"></i>
                                <i data-id="<?php echo $task['id'] ?>" class="fas fa-trash-alt deleteBtn"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="empty-tasks">No tasks yet.</span>
                <?php endif; ?>
            </div>
            <div class="dataSearch"></div>
        </div>
        <div class="tasks-statis">
            <div class="count"><span class="all-tasks"><?php $tasks['pagination']['total'] ?></span> Total Tasks.</div>
            <div class="completed">
                <span class="completed-tasks"><?php $taskDone ?></span> Tasks compeled.
            </div>
        </div>
    </div>
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create Task</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" class="form-control" id="taskId">
                    </div>
                    <div class="form-group">
                        <label for="name">Name :</label>
                        <input type="text" class="form-control" id="name" placeholder="Enter name task" required>
                    </div>
                    <div class="form-group">
                        <label for="priority">Priority :</label>
                        <select name="priority" id="priority" class="form-control" required>
                            <?php foreach ($priority as $key => $value): ?>
                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group statusDiv">
                        <label for="status">Status :</label>
                        <select name="status" id="status" class="form-control" required>
                            <?php foreach ($status as $key => $value): ?>
                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="save">Save changes</button>
                    <button type="button" class="btn btn-primary hidden" id="edit">Edit changes</button>
                </div>
            </div>

        </div>
    </div>

    <div id="view" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">View Task</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" class="form-control" id="view-taskId">
                    </div>
                    <div class="form-group">
                        <label for="name">Name :</label>
                        <input type="text" class="form-control" readonly id="view-name" placeholder="Enter name task"
                               required>
                    </div>
                    <div class="form-group">
                        <label for="priority">Priority :</label>
                        <select name="priority" id="view-priority" disabled class="form-control" required>
                            <?php foreach ($priority as $key => $value): ?>
                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status :</label>
                        <select name="priority" id="view-status" disabled class="form-control" required>
                            <?php foreach ($status as $key => $value): ?>
                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>