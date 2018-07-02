<template>
    <div class="tasks">
        <div class="container">
            <div class="row">
                <div class="col-md-5 media" style="width: 100%">
                    <!-- Show if there are tasks in the array -->
                    <ul v-if="tasks" class="media-body">
                        <button type="button" class="btn btn-link">List</button> | <button type="button" class="btn btn-link">Calendar</button>
                        <li v-for="task in tasks">
                            <strong>Title: </strong>{{ task.title }} |
                            <button class="btn btn-sm btn-outline-primary ml-3" @click="showTask(task.id)">Edit</button> |
                            <button class="btn btn-sm btn-outline-danger ml-3" @click="deleteTask(task.id, task.title)">Delete</button>
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
                            <div v-if="imageData.length > 0">
                                <img width="200px" :src=imageData />
                            </div>
                            <input type="file" class="form-control" ref="fileToUpload" v-on:change="previewFile" />
                            <br />
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
                            <!-- <span class="downloadLink" @click="downloadAttachment"></span> -->
                            <h5 v-if="task.attachment"><b>Download:</b><a :href="task.attachment" download>{{ attachmentName }}</a></h5>
                            <div v-if="imageData.length == 0">
                                <img v-if="task.attachment && isImage" width="200px" :src=task.attachment />
                                <img v-else width="200px" src="storage/No_Image_Available.png"  />
                            </div>
                            <div v-if="imageData.length > 0">
                                <img width="200px" :src=imageData />
                            </div>
                            <input type="file" class="form-control" ref="fileToUpload" v-on:change="previewFile" />
                            <br />
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
        <!-- Information boxes -->
        <div class="container fixed-bottom">
            <div class="float-right">
                <div v-if="inputInfo != ''" class="alert alert-info alert-dismissible fade show" role="alert">
                    <ul v-html="inputInfo">
                        {{ inputInfo }}
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div v-if="saveInfo != ''" class="alert alert-success alert-dismissible fade show" role="alert">
                        <ul v-html="saveInfo">
                            {{ saveInfo }}
                        </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div v-if="deleteInfo != ''" class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ deleteInfo }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div v-if="error != ''" class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ error }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
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
                error: '',
                saveInfo: '',
                inputInfo: '',
                deleteInfo: '',
                edit: false,
                imageData: "",
                isImage: true,
                task: {
                    id: '',
                    title: '',
                    description: '',
                    starts_at: '',
                    ends_at: '',
                    attachment: null,
                },
                files: [],
                imageTypes:["bmp", "gif", "jpe", "jpeg", "jpg", "svg", "png"],
                attachmentName: "",
            };
        },
        created() {
            this.fetchData();
        },
        methods: {
            fetchData() {
                this.error = '';
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

            // Show the task the user wants to edit or see
            showTask(id) {
                this.imageData = '';
                this.saveInfo = '';
                this.loading = true;
                this.edit = true;
                this.task.id = id;
                this.isImage = false;
                axios
                    .get('/api/task/' + this.task.id)
                    .then(response => {
                        response.data.starts_at = response.data.starts_at.replace(' ', 'T');
                        response.data.ends_at = response.data.ends_at.replace(' ', 'T');
                        this.task = response.data;
                        this.loading = false;
                        let fileSplitted = this.task.attachment.split('.').join('/').split('/');
                        this.attachmentName = fileSplitted[fileSplitted.length-2];
                        this.attachmentName = this.attachmentName.split('_').splice(1).join("_");
                        let fileExt = fileSplitted[fileSplitted.length-1];
                        if(this.imageTypes.indexOf(fileExt) != -1) {
                            this.isImage = true;
                        }
                    });
            },

            // Close the task and show the create new task
            hideTask() {
                this.saveInfo = '';
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

            // Save the edited task
            updateTask() {
                //TODO: Validate postdata
                this.inputInfo = "";
                // Checking if everything is filled in
                if(this.task.title == "") {
                    this.inputInfo += "<li>A <b>title</b> is required</li>"
                }
                if(this.task.description == "") {
                    this.inputInfo += "<li>A <b>description</b> is required</li>"
                }
                if(this.task.starts_at == "") {
                    this.inputInfo += "<li>A <b>start date</b> is required</li>"
                }
                if(this.task.ends_at == "") {
                    this.inputInfo += "<li>A <b>end date</b> is required</li>"
                }
                if(this.task.starts_at > this.task.ends_at) {
                    this.inputInfo += "<li>Start date cannot be greater than end date</li>"
                }

                // Send data if everything is fine
                if(this.inputInfo == "") {
                    const formData = new FormData();
                    formData.set('title', this.task.title);
                    formData.set('description', this.task.description);
                    formData.set('starts_at', this.task.starts_at);
                    formData.set('ends_at', this.task.ends_at);
                    formData.set('_method', 'PATCH');
                    formData.append('attachment', this.task.attachment, this.task.attachment.name);
                    axios.post('/api/task/' + this.task.id, formData)
                        .then(response => {
                            this.saveInfo = "Task saved successfully!"
                            this.fetchData();
                        });
                }
            },

            // Save a new created task
            createTask() {
                //TODO: validate postdata
                this.inputInfo = "";
                // Checking if everything is filled in
                if(this.task.title == "") {
                    this.inputInfo += "<li>A <b>title</b> is required to make a new task</li>"
                }
                if(this.task.description == "") {
                    this.inputInfo += "<li>A <b>description</b> is required to make a new task</li>"
                }
                if(this.task.starts_at == "") {
                    this.inputInfo += "<li>A <b>start date</b> is required to make a new task</li>"
                }
                if(this.task.ends_at == "") {
                    this.inputInfo += "<li>A <b>end date</b> is required to make a new task</li>"
                }
                if(this.task.starts_at > this.task.ends_at) {
                    this.inputInfo += "<li>Start date cannot be greater than end date</li>"
                }

                // Send data if everything is fine
                if(this.inputInfo == "") {
                    const formData = new FormData();
                    formData.append('title', this.task.title);
                    formData.append('description', this.task.description);
                    formData.append('starts_at', this.task.starts_at);
                    formData.append('ends_at', this.task.ends_at);
                    formData.append('attachment', this.task.attachment, this.task.attachment.name);
                    axios
                        .post('/api/task', formData)
                        .then(response => {
                            this.saveInfo = "New task created successfully!"
                            this.fetchData();
                        });
                }
            },

            // Delete the task selected by the user
            deleteTask(id, taskTitle) {
                axios
                    .delete('/api/task/' + id, {data: { id: id}})
                    .then(response => {
                        this.deleteInfo = "Task " + taskTitle +  " deleted";
                        this.fetchData();
                    }).catch(error => {
                        this.loading = false;
                        this.error = error.response.data.message || error.message;
                    });
            },

            // Source code https://jsfiddle.net/mani04/5zyozvx8/
            // Preview an image when the uploaded file is an image
            previewFile(event) {
                this.task.attachment = this.$refs.fileToUpload.files[0];

                var input = event.target;
                if((/image*/.test(input.files[0].type))) {
                    if(input.files && input.files[0]) {
                        var reader = new FileReader();
                        reader.onload = (e) => {
                            this.imageData = e.target.result;
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                } else {
                    this.imageData = "storage/No_Image_Available.png";
                }
            },
        }
    }
</script>
