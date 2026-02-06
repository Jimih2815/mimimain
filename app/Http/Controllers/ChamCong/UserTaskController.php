<?php

namespace App\Http\Controllers\ChamCong;

use App\Http\Controllers\Controller;
use App\Models\ChamCong\Task;
use App\Models\ChamCong\TaskAssignee;
use App\Models\ChamCong\TaskProgress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserTaskController extends Controller
{
    public function index(Request $request)
    {
        $userId = (int) $request->session()->get('chamcong_user_id');

        TaskAssignee::where('user_id', $userId)
            ->where('seen', 0)
            ->update(['seen' => 1]);

        $popupMsg = $request->session()->pull('chamcong_popup_msg');

        $myTasksAll = DB::connection('chamcong')
            ->table('task_assignees as ta')
            ->join('tasks as t', 'ta.task_id', '=', 't.id')
            ->where('ta.user_id', $userId)
            ->select('t.*')
            ->orderByDesc('t.id')
            ->get();

        $pendingTasks = [];
        $completedTasks = [];
        foreach ($myTasksAll as $task) {
            if (empty($task->completed_at)) {
                $pendingTasks[] = $task;
            } else {
                $completedTasks[] = $task;
            }
        }

        $taskIds = collect($myTasksAll)->pluck('id')->all();
        $subTaskByTask = [];
        if (!empty($taskIds)) {
            $subTasks = TaskProgress::whereIn('task_id', $taskIds)
                ->orderBy('id')
                ->get();
            foreach ($subTasks as $sub) {
                $subTaskByTask[$sub->task_id][] = $sub;
            }
        }

        return view('chamcong.tasks', [
            'pendingTasks' => $pendingTasks,
            'completedTasks' => $completedTasks,
            'subTaskByTask' => $subTaskByTask,
            'popupMsg' => $popupMsg,
        ]);
    }

    public function completeSubtask(Request $request)
    {
        $request->validate([
            'progress_id' => ['required','integer'],
        ]);

        $progressId = (int) $request->input('progress_id');
        $tz = config('chamcong.timezone', 'Asia/Ho_Chi_Minh');
        $now = Carbon::now($tz)->format('Y-m-d H:i:s');

        TaskProgress::where('id', $progressId)
            ->update([
                'is_completed' => 1,
                'completed_at' => $now,
            ]);

        $progress = TaskProgress::find($progressId);
        if ($progress) {
            $taskId = (int) $progress->task_id;

            $pending = TaskProgress::where('task_id', $taskId)
                ->where('is_completed', 0)
                ->count();

            if ($pending === 0) {
                $task = Task::find($taskId);
                if ($task) {
                    $completionLog = '';
                    $popupMsg = '';
                    if (!empty($task->due_date)) {
                        $dueTs = strtotime($task->due_date);
                        $completeTs = strtotime($now);
                        if ($completeTs <= $dueTs) {
                            $completionLog = 'Công việc hoàn thành đúng hạn';
                            $popupMsg = 'Chúc mừng, bạn đã hoàn thành xong công việc';
                        } else {
                            $completionLog = 'Công việc hoàn thành chậm hơn dự kiến';
                            $popupMsg = 'Bạn đã hoàn thành công việc chậm hơn dự định';
                        }
                    } else {
                        $completionLog = 'Công việc đã hoàn thành';
                        $popupMsg = 'Bạn đã hoàn thành công việc';
                    }

                    $task->completed_at = $now;
                    $task->completion_log = $completionLog;
                    $task->save();

                    $request->session()->put('chamcong_popup_msg', $popupMsg);
                    return redirect()->route('chamcong.tasks', [], 302)->withFragment('task-' . $taskId);
                }
            }

            return redirect()->route('chamcong.tasks', [], 302)->withFragment('task-' . $taskId);
        }

        return redirect()->route('chamcong.tasks');
    }
}
