<?php

namespace App\Controllers;

use App\Exceptions\ModelNotFoundException;
use App\Models\Task;
use App\Services\Database\DB;
use App\Services\Validation\Rules\IntegerRule;
use App\Services\Validation\Rules\MaxLengthRule;
use App\Services\Validation\Rules\RequireRule;
use App\Services\Validation\Rules\StringRule;
use PDOException;

class ToDoController extends BaseController
{

    const PRIORITY = [
        1 => 'High',
        2 => 'Normal',
        3 => 'Low'
    ];

    const STATUS_TASK = [
        0 => 'Not Done',
        1 => 'Done',
    ];

    /**
     *  show all task
     * @return mixed
     */
    public function index()
    {
        $task = new Task();
        $conditions = [
            'status' => '1'
        ];
        $taskDone = $task->findAllBy($conditions)->toArray();
        $tasks = $task
            ->paginate(
                $this->request->getInput('page', 1),
                $this->request->getInput('per_page', 20),
                [
                    'priority' => 'ASC',
                    'name' => 'ASC'
                ]
            )
            ->toArray('tasks');
        
        if($this->request->isAjax()){
            $param = $this->request->all();
            $tasks = $task
                ->paginate(
                    $this->request->getInput('page', 1),
                    $this->request->getInput('per_page', 20),
                    [
                        'priority' => $param['sortByPriority'],
                        'name' => $param['sortByName']
                    ]
                )
                ->toArray('tasks');
            $tasks['priority'] = self::PRIORITY;
            $tasks['status'] = self::STATUS_TASK;
            
            return $this->response->success("OK", $tasks);
        }
        return $this->response->view('task/index.php',[
            'tasks' => $tasks,
            'priority' => self::PRIORITY,
            'status' => self::STATUS_TASK,
            'taskDone' => $taskDone
        ]);
    }

    /**
     * @throws \App\Exceptions\ModelNotFoundException
     */
    public function create()
    {
        $rules = [
            'name' => [
                new RequireRule(),
                new StringRule(),
                new MaxLengthRule(150)
            ],
            'priority' => [
                new RequireRule(),
                new IntegerRule(),
                new MaxLengthRule(1)
            ],
        ];
        
        $this->request->validate($rules);

        $task = new Task();
        
        $data = [
          'name' => $this->request->getInput('name'),
          'status' => 0,
          'priority' => $this->request->getInput('priority'),
        ];
        
        DB::getInstance()->beginTransaction();

        try {
            $task->create($data);
            DB::getInstance()->commit();
        } catch (PDOException $e) {
            DB::getInstance()->rollback();
            throw $e;
        }

        return $this->response->created(
            "Task {$task->getAttribute('name')} created",
            $task->toArray()
        );
    }

    /**
     * @return mixed
     */
    public function finish()
    {
        $rules = [
            'taskId' => [
                new RequireRule(),
                new StringRule(),
                new MaxLengthRule(150)
            ],
        ];
        $this->request->validate($rules);
        
        try {
            $requestData = $this->request->getPostData();
            $taskId = $requestData['taskId'];

            $task = (new Task())->find($taskId);
            $arrayTask = $task->toArray();
            
            DB::getInstance()->beginTransaction();

            try {
                $arrayTask['status'] = 1;
                $task->update($arrayTask);
                DB::getInstance()->commit();
            } catch (PDOException $e) {
                DB::getInstance()->rollback();
            }
        } catch (ModelNotFoundException $e) {
            return $this->response->notFound("Task not found");
        }
    }

    public function update()
    {
        $rules = [
            'taskId' => [
                new RequireRule(),
                new StringRule(),
                new MaxLengthRule(150)
            ],
            'name' => [
                new RequireRule(),
                new StringRule(),
                new MaxLengthRule(150)
            ],
            'priority' => [
                new RequireRule(),
                new StringRule(),
                new MaxLengthRule(1)
            ],
            'status' => [
                new RequireRule(),
                new StringRule(),
                new MaxLengthRule(1)
            ],
        ];
        
        $this->request->validate($rules);
        try {
            $requestData = $this->request->getPostData();
            $taskId = $requestData['taskId'];

            $task = (new Task())->find($taskId);
            $arrayTask = $task->toArray();

            DB::getInstance()->beginTransaction();

            try {
                $arrayTask['name'] = $requestData['name'];
                $arrayTask['priority'] = $requestData['priority'];
                $task->update($this->request->all());
                DB::getInstance()->commit();
            } catch (PDOException $e) {
                DB::getInstance()->rollback();
                throw $e;
            }
        } catch (ModelNotFoundException $e) {
            return $this->response->notFound("Task not found");
        }

        return $this->response->success(
            "Task {$task->getAttribute('name')} updated",
            $task->toArray()
        );
    }

    public function delete()
    {
        try {
            $requestData = $this->request->getPostData();
            $taskId = $requestData['taskId'];

            $task = (new Task())->find($taskId);

            DB::getInstance()->beginTransaction();

            try {
                $task->delete();
                DB::getInstance()->commit();
            } catch (PDOException $e) {
                DB::getInstance()->rollback();
            }
        } catch (ModelNotFoundException $e) {
            return $this->response->notFound("Task not found");
        }

        return $this->response->success("Deleted");
    }
}