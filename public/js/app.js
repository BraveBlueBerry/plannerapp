var data = {
    page: "overview",
    nothome: "hoi",
    tasks: [],
    currentTask: "",
    nameTask: ""
}

var app = new Vue({
    el: "#app",
    data: data,
    methods: {
        showEditTask: function (task, event) {
            app.currentTask = task;
            app.page = "editTask";
        },
        overview: function(event) {
            app.init();
            app.page = "overview";
        },
        showNewTask: function(event) {
            app.page = "newTask";
        },
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
