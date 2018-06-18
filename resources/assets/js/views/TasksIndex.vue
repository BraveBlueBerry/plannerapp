<template>
    <div class="tasks">

        <div v-if="error" class="error">
            <p>
                {{ error }}
            </p>
            <p>
                <button @click.prevent="fetchData">
                    Try Again
                </button>
            </p>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-5 media" style="width: 100%">
                    <ul v-if="tasks" class="media-body">
                        <button type="button" class="btn btn-link">List</button> | <button type="button" class="btn btn-link">Calendar</button>
                        <li v-for="task in tasks">
                            <strong>Title: </strong> {{ task.title }} |
                            <button class="btn btn-sm btn-outline-primary ml-3" @click="showTask(task.id)">Edit</button>
                        </li>
                    </ul>
                </div>
                <div class="col-md-5" style="width: 100%">
                    <div class="editing" v-if="edit">
                        <div class="form-group">
                            <input class="form-control" v-model="task.title" />
                            <br />
                            <textarea class="form-control" v-model="task.description"></textarea>
                            <br />
                            <input type="datetime-local" class="form-control" v-model="task.starts_at" />
                            <br />
                            <input type="datetime-local" class="form-control" v-model="task.ends_at" />
                            <br />
                            <div v-if="save_info != ''">
                                {{ save_info }}
                            </div>
                            <button @click="updateTask()">Save</button>
                            <button @click="hideTask()">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="loading" v-if="loading">
            Loading..
        </div>
    </div>
</template>
<script>
    import axios from 'axios';
    export default {
        data() {
            return {
                loading: false,
                tasks: null,
                error: null,
                save_info: '',
                edit: false,
                task: {
                    id: '',
                    title: '',
                    description: '',
                    starts_at: '',
                    ends_at: '',
                }
            };
        },
        created() {
            this.fetchData();
        },
        methods: {
            fetchData() {
                this.error = this.tasks = null;
                this.loading = true;
                axios
                    .get('/api/tasks')
                    .then(response => {
                        this.loading = false;
                        this.tasks = response.data;
                    }).catch(error => {
                        this.loading = false;
                        this.error = error.response.data.message || error.message;
                    });
            },

            showTask(id) {
                this.save_info = '';
                this.edit = true;
                this.task.id = id;
                this.loading = true;
                axios
                    .get('/api/task/' + this.task.id)
                    .then(response => {
                        response.data.starts_at = response.data.starts_at.replace(' ', 'T');
                        response.data.ends_at = response.data.ends_at.replace(' ', 'T');
                        this.task = response.data;
                        this.loading = false;
                    });
            },

            hideTask() {
                this.save_info = '';
                this.edit = false;
            },

            updateTask() {
                //TODO: Validate postdata
                var temp_starts_at = this.task.starts_at.replace('T', ' ');
                var temp_ends_at = this.task.ends_at.replace('T', ' ');
                console.log(this.task.starts_at);
                axios
                    .patch('/api/task/' + this.task.id, {
                        title: this.task.title,
                        description: this.task.description,
                        starts_at: this.task.starts_at,
                        ends_at: this.task.ends_at,
                    })
                    .then(response => {
                        this.save_info = "Task saved successfully!"
                    });
            },
        }
    }
</script>
