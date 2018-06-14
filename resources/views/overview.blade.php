List of tasks for the planning:

<ul v-for="task in tasks">
    <li>@{{ task.title }}</li>
    <button type="button" v-on:click="showEditTask(task, $event)">edit</button>
</ul>

<button type="button" v-on:click="showNewTask">New task</button>
