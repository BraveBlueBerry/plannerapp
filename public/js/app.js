var data = {
    page: "overview",
    nothome: "hoi",
    tasks: [],
    currentTask: ""
}

var app = new Vue({
    el: "#app",
    data: data,
    methods: {
        //Go to edit task page with currentTask
        showEditTask: function (task, event) {
            app.currentTask = task;
            app.page = "editTask";
        },
        //Go to the overview
        overview: function(event) {
            app.init();
            app.page = "overview";
        },
        //Go to new task page
        showNewTask: function(event) {
            app.page = "newTask";
        },
        //Get all tasks
        init: function(event) {
            axios.get('/api/tasks')
                .then(function(response) {
                    app.tasks = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        }
    },
    mounted(){
        this.init();
    }
});
