<template>
    <div class="tasks">
        <!-- Show if there's an error -->
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
                    <!-- Show if there are tasks in the array -->
                    <ul v-if="tasks" class="media-body">
                        <button type="button" class="btn btn-link">List</button> | <button type="button" class="btn btn-link">Calendar</button>
                        <li v-for="task in tasks">
                            <strong>Title: </strong>{{ task.title }} |
                            <button class="btn btn-sm btn-outline-primary ml-3" @click="showTask(task.id)">Edit</button>
                        </li>
                    </ul>
                </div>
                <div class="col-md-5" style="width: 100%">
                    <!-- Show if the user has no task selected to edit or show -->
                    <div class="newTask" v-if="!edit">
                        <h2>Create a new task</h2>
                        <div class="form-group">
                            <input class="form-control" v-model="task.title" placeholder="Title" />
                            <br />
                            <textarea class="form-control" v-model="task.description" placeholder="Description"></textarea>
                            <br />
                            <input type="datetime-local" class="form-control" v-model="task.starts_at" />
                            <br />
                            <input type="datetime-local" class="form-control" v-model="task.ends_at" />
                            <br />
                            <input type="file" class="form-control" ref="fileToUpload" v-on:change="previewFile" />
                            <br />
                            <div v-if="save_info != ''">
                                <ul v-html="save_info">
                                    {{ save_info }}
                                </ul>
                            </div>
                            <div v-if="input_info != ''">
                                <ul v-html="input_info">
                                    {{ input_info }}
                                </ul>
                            </div>
                            <button @click="createTask()">Save</button>
                        </div>
                    </div>
                    <!-- Show if the user has selected a task to edit or show -->
                    <div class="editing" v-if="edit">
                        <h2>Edit task: <i>{{ task.title }}</i></h2>
                        <div class="form-group">
                            <input class="form-control" v-model="task.title" />
                            <br />
                            <textarea class="form-control" v-model="task.description"></textarea>
                            <br />
                            <input type="datetime-local" class="form-control" v-model="task.starts_at" />
                            <br />
                            <input type="datetime-local" class="form-control" v-model="task.ends_at" />
                            <br />
                            <div v-if="imageData.length == 0">
                                <img v-if="task.attachment" width="100" height="100":src=task.attachment />
                            </div>
                            <div v-if="imageData.length > 0">
                                <img width="100" height="100":src=imageData />
                            </div>
                            <input type="file" class="form-control" ref="fileToUpload" v-on:change="previewFile" />
                            <br />
                            <div v-if="save_info != ''">
                                <ul v-html="save_info">
                                    {{ save_info }}
                                </ul>
                            </div>
                            <!-- Any conflicting info about the task the user wants to safe -->
                            <div v-if="input_info != ''">
                                <ul v-html="input_info">
                                    {{ input_info }}
                                </ul>
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
    import axios from 'axios'

    export default {
        data() {
            return {
                loading: false,
                tasks: null,
                error: null,
                save_info: '',
                input_info: '',
                edit: false,
                imageData: "",
                task: {
                    id: '',
                    title: '',
                    description: '',
                    starts_at: '',
                    ends_at: '',
                    attachment: null,
                },
                files: [],
            };
        },
        created() {
            this.fetchData();
        },
        methods: {
            fetchData() {
                this.error = null;
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
                this.imageData = "";
                this.save_info = '';
                this.loading = true;
                this.edit = true;
                this.task.id = id;
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
                this.task = {
                    id: '',
                    title: '',
                    description: '',
                    starts_at: '',
                    ends_at: '',
                    attachment: null,
                }
            },

            updateTask() {
                //TODO: Validate postdata
                this.input_info = "";
                // Checking if everything is filled in
                if(this.task.title == "") {
                    this.input_info += "<li>A <b>title</b> is required</li>"
                }
                if(this.task.description == "") {
                    this.input_info += "<li>A <b>description</b> is required</li>"
                }
                if(this.task.starts_at == "") {
                    this.input_info += "<li>A <b>start date</b> is required</li>"
                }
                if(this.task.ends_at == "") {
                    this.input_info += "<li>A <b>end date</b> is required</li>"
                }
                if(this.task.starts_at > this.task.ends_at) {
                    this.input_info += "<li>Start date cannot be greater than end date</li>"
                }

                // Send data if everything is fine
                if(this.input_info == "") {
                    const formData = new FormData();
                    formData.set('title', this.task.title);
                    formData.set('description', this.task.description);
                    formData.set('starts_at', this.task.starts_at);
                    formData.set('ends_at', this.task.ends_at);
                    formData.set('_method', 'PATCH');
                    formData.append('attachment', this.task.attachment, this.task.attachment.name);
                    axios.post('/api/task/' + this.task.id, formData)
                        .then(response => {
                            this.save_info = "Task saved successfully!"
                            this.fetchData();
                        });
                }
            },

            createTask() {
                //TODO: validate postdata
                this.input_info = "";
                // Checking if everything is filled in
                if(this.task.title == "") {
                    this.input_info += "<li>A <b>title</b> is required to make a new task</li>"
                }
                if(this.task.description == "") {
                    this.input_info += "<li>A <b>description</b> is required to make a new task</li>"
                }
                if(this.task.starts_at == "") {
                    this.input_info += "<li>A <b>start date</b> is required to make a new task</li>"
                }
                if(this.task.ends_at == "") {
                    this.input_info += "<li>A <b>end date</b> is required to make a new task</li>"
                }
                if(this.task.starts_at > this.task.ends_at) {
                    this.input_info += "<li>Start date cannot be greater than end date</li>"
                }

                // Send data if everything is fine
                if(this.input_info == "") {
                    const formData = new FormData();
                    formData.append('title', this.task.title);
                    formData.append('description', this.task.description);
                    formData.append('starts_at', this.task.starts_at);
                    formData.append('ends_at', this.task.ends_at);
                    formData.append('attachment', this.task.attachment, this.task.attachment.name);
                    axios
                        .post('/api/task', formData)
                        .then(response => {
                            this.save_info = "New task created successfully!"
                            this.fetchData();
                        });
                }
            },
            // Source code https://jsfiddle.net/mani04/5zyozvx8/
            previewFile(event) {
                this.task.attachment = this.$refs.fileToUpload.files[0];

                var input = event.target;

                if(input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = (e) => {
                        this.imageData = e.target.result;
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        }
    }
</script>
