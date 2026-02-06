<?php

namespace App\Http\Controllers\ChamCong\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChamCong\ChamCongUser;
use App\Models\ChamCong\Task;
use App\Models\ChamCong\TaskAssignee;
use App\Models\ChamCong\TaskProgress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskAdminController extends Controller
{
    public function index(Request $request)
    {
        $allUsers = ChamCongUser::select('id', 'username')->orderBy('username')->get();
        $allUsersMap = $allUsers->pluck('username', 'id')->toArray();

        $allTasks = DB::connection('chamcong')
            ->table('tasks as t')
            ->select('t.*', DB::raw('(
                SELECT GROUP_CONCAT(u.username SEPARATOR ", ")
                FROM task_assignees ta
                JOIN users u ON ta.user_id = u.id
                WHERE ta.task_id = t.id
            ) as assignees'))
            ->orderByDesc('t.id')
            ->get();

        $pendingTasks = [];
        $completedTasks = [];
        foreach ($allTasks as $task) {
            if (empty($task->completed_at)) {
                $pendingTasks[] = $task;
            } else {
                $completedTasks[] = $task;
            }
        }

        $allSub = TaskProgress::orderBy('id')->get();
        $subTasksByTask = [];
        foreach ($allSub as $st) {
            $subTasksByTask[$st->task_id][] = $st;
        }

        $assigneesByTaskId = [];
        $allTa = TaskAssignee::select('task_id', 'user_id')->get();
        foreach ($allTa as $ta) {
            $assigneesByTaskId[$ta->task_id][] = $ta->user_id;
        }

        $newTasksForPopup = Task::whereNotNull('completed_at')
            ->where(function ($q) {
                $q->whereNull('admin_popup_shown')->orWhere('admin_popup_shown', 0);
            })
            ->get();

        if ($newTasksForPopup->isNotEmpty()) {
            Task::whereIn('id', $newTasksForPopup->pluck('id')->all())
                ->update(['admin_popup_shown' => 1]);
        }

        $newTasksForPopup = $newTasksForPopup->map(function ($task) use ($assigneesByTaskId, $allUsersMap) {
            $names = [];
            foreach ($assigneesByTaskId[$task->id] ?? [] as $uid) {
                if (!empty($allUsersMap[$uid])) {
                    $names[] = $allUsersMap[$uid];
                }
            }
            $task->assignees = implode(', ', $names);
            return $task;
        });

        return view('chamcong.admin.tasks', [
            'allUsers' => $allUsers,
            'allUsersMap' => $allUsersMap,
            'pendingTasks' => $pendingTasks,
            'completedTasks' => $completedTasks,
            'subTasksByTask' => $subTasksByTask,
            'assigneesByTaskId' => $assigneesByTaskId,
            'newTasksForPopup' => $newTasksForPopup,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_name' => ['required','string'],
            'task_content' => ['required','string'],
            'due_date' => ['nullable','date'],
            'general_note' => ['nullable','string'],
        ]);

        $tz = config('chamcong.timezone', 'Asia/Ho_Chi_Minh');
        $now = Carbon::now($tz)->format('Y-m-d H:i:s');

        $task = Task::create([
            'task_name' => trim($request->input('task_name')),
            'task_content' => trim($request->input('task_content')),
            'due_date' => $request->input('due_date') ?: null,
            'general_note' => trim($request->input('general_note')),
            'created_by' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $assignees = $request->input('assignees', []);
        if (!empty($assignees)) {
            foreach ($assignees as $uid) {
                TaskAssignee::create([
                    'task_id' => $task->id,
                    'user_id' => (int) $uid,
                    'assigned_at' => $now,
                    'seen' => 0,
                ]);
            }
        }

        $progressContent = $request->input('progress_content', []);
        $progressNote = $request->input('progress_note', []);
        $progressDue = $request->input('progress_due_date', []);

        for ($i = 0; $i < count($progressContent); $i++) {
            $pContent = trim($progressContent[$i] ?? '');
            if ($pContent === '') {
                continue;
            }
            TaskProgress::create([
                'task_id' => $task->id,
                'progress_content' => $pContent,
                'progress_note' => trim($progressNote[$i] ?? ''),
                'due_date' => !empty($progressDue[$i]) ? $progressDue[$i] : null,
                'is_completed' => 0,
            ]);
        }

        $request->session()->flash('chamcong_flash_msg', 'Đã tạo công việc mới và giao cho các nhân viên thành công!');
        return redirect()->route('chamcong.admin.tasks');
    }

    public function update(Request $request)
    {
        $request->validate([
            'task_id' => ['required','integer'],
            'task_name' => ['required','string'],
            'task_content' => ['required','string'],
            'due_date' => ['nullable','date'],
            'general_note' => ['nullable','string'],
        ]);

        $taskId = (int) $request->input('task_id');
        Task::where('id', $taskId)->update([
            'task_name' => trim($request->input('task_name')),
            'task_content' => trim($request->input('task_content')),
            'due_date' => $request->input('due_date') ?: null,
            'general_note' => trim($request->input('general_note')),
        ]);

        $updateIds = $request->input('update_progress_id', []);
        $updateContent = $request->input('update_progress_content', []);
        $updateNote = $request->input('update_progress_note', []);
        $updateDue = $request->input('update_progress_due', []);

        for ($i = 0; $i < count($updateIds); $i++) {
            $pid = (int) ($updateIds[$i] ?? 0);
            if ($pid <= 0) {
                continue;
            }
            TaskProgress::where('id', $pid)->update([
                'progress_content' => trim($updateContent[$i] ?? ''),
                'progress_note' => trim($updateNote[$i] ?? ''),
                'due_date' => !empty($updateDue[$i]) ? $updateDue[$i] : null,
            ]);
        }

        $newContents = $request->input('new_progress_content', []);
        $newNotes = $request->input('new_progress_note', []);
        $newDues = $request->input('new_progress_due', []);
        for ($i = 0; $i < count($newContents); $i++) {
            $content = trim($newContents[$i] ?? '');
            if ($content === '') {
                continue;
            }
            TaskProgress::create([
                'task_id' => $taskId,
                'progress_content' => $content,
                'progress_note' => trim($newNotes[$i] ?? ''),
                'due_date' => !empty($newDues[$i]) ? $newDues[$i] : null,
                'is_completed' => 0,
            ]);
        }

        TaskAssignee::where('task_id', $taskId)->delete();
        $assignees = $request->input('assignees', []);
        if (!empty($assignees)) {
            $tz = config('chamcong.timezone', 'Asia/Ho_Chi_Minh');
            $now = Carbon::now($tz)->format('Y-m-d H:i:s');
            foreach ($assignees as $uid) {
                TaskAssignee::create([
                    'task_id' => $taskId,
                    'user_id' => (int) $uid,
                    'assigned_at' => $now,
                    'seen' => 0,
                ]);
            }
        }

        $request->session()->flash('chamcong_flash_msg', 'Đã cập nhật công việc thành công!');
        return redirect()->route('chamcong.admin.tasks', [], 302)->withFragment('task-' . $taskId);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'task_id' => ['required','integer'],
        ]);

        $taskId = (int) $request->input('task_id');
        TaskProgress::where('task_id', $taskId)->delete();
        TaskAssignee::where('task_id', $taskId)->delete();
        Task::where('id', $taskId)->delete();

        $request->session()->flash('chamcong_flash_msg', "Đã xóa công việc ID {$taskId} thành công.");
        return redirect()->route('chamcong.admin.tasks', ['tab' => 'quanly']);
    }

    public function deleteProgress(Request $request)
    {
        $request->validate([
            'progress_id' => ['required','integer'],
        ]);

        $pid = (int) $request->input('progress_id');
        TaskProgress::where('id', $pid)->delete();

        $request->session()->flash('chamcong_flash_msg', "Đã xóa tiến độ ID {$pid}");
        return redirect()->route('chamcong.admin.tasks', ['tab' => 'quanly']);
    }
}
