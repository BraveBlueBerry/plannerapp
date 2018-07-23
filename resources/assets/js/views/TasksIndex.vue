<template>
    <div class="tasks">
        <div class="container">
            <div class="row">
                <div class="col-md-5 media" style="width: 100%">
                    <!-- Show if there are tasks in the array -->
                    <ul v-if="tasks">
                        <button type="button" class="btn btn-link">List</button> | <button type="button" class="btn btn-link">Calendar</button>
                        <li id="tasks-list" v-for="task in tasks">
                            <div class="blue-container row">
                                <div class="task-title col-10">
                                    {{ task.title }}
                                </div>
                                <div class="task-buttons">
                                    <button class="btn btn-sm btn-outline-primary ml-3" @click="showTask(task.id)">Edit</button>
                                    <button class="btn btn-sm btn-outline-danger ml-3" @click="deleteTask(task.id, task.title)">Delete</button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col-md-5" style="width: 100%">
                    <!-- Show if the user has no task selected to edit or show -->
                    <div class="newTask" v-if="!edit">
                        <h2>Create a new task</h2>
                        <div class="form-group blue-container">
                            <input class="form-control" v-model="task.title" placeholder="Title" />
                            <br />
                            <textarea class="form-control" v-model="task.description" placeholder="Description"></textarea>
                            <br />
                            <!-- Datepicker -->
                            <select v-model="date.from.day">
                                <option v-bind:value="day" v-for="day in days">{{ day }}</option>
                            </select>
                            <select v-model="date.from.month" v-on:change="changeMonth">
                                <option v-bind:value="month.id" v-for="month in months">{{ month.name }}</option>
                            </select>
                            <select v-model="date.from.year">
                                <option v-bind:value="year" v-for="year in years">{{ year }}</option>
                            </select>
                            <input class="time" type="text" v-model="time.from.hour" /> :
                            <input class="time" type="text" v-model="time.from.minute" />
                            <br />
                            <select v-model="date.to.day">
                                <option v-bind:value="day" v-for="day in days">{{ day }}</option>
                            </select>
                            <select v-model="date.to.month" v-on:change="changeMonth">
                                <option v-bind:value="month.id" v-for="month in months">{{ month.name }}</option>
                            </select>
                            <select v-model="date.to.year">
                                <option v-bind:value="year" v-for="year in years">{{ year }}</option>
                            </select>
                            <input class="time" type="text" v-model="time.to.hour" /> :
                            <input class="time" type="text" v-model="time.to.minute" />
                            <!-- End Datepicker-->
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
                            <!-- Datepicker -->
                            <select v-model="date.from.day">
                                <option v-bind:value="day" v-for="day in days">{{ day }}</option>
                            </select>
                            <select v-model="date.from.month" v-on:change="changeMonth">
                                <option v-bind:value="month.id" v-for="month in months">{{ month.name }}</option>
                            </select>
                            <select v-model="date.from.year">
                                <option v-bind:value="year" v-for="year in years">{{ year }}</option>
                            </select>
                            <input class="time" type="text" v-model="time.from.hour" /> :
                            <input class="time" type="text" v-model="time.from.minute" />
                            <br />
                            <select v-model="date.to.day">
                                <option v-bind:value="day" v-for="day in days">{{ day }}</option>
                            </select>
                            <select v-model="date.to.month" v-on:change="changeMonth">
                                <option v-bind:value="month.id" v-for="month in months">{{ month.name }}</option>
                            </select>
                            <select v-model="date.to.year">
                                <option v-bind:value="year" v-for="year in years">{{ year }}</option>
                            </select>
                            <input class="time" type="text" v-model="time.to.hour" /> :
                            <input class="time" type="text" v-model="time.to.minute" />
                            <!-- End Datepicker-->
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
                    <ul v-html="inputInfo"></ul>
                    <button type="button" class="close" v-on:click="inputInfo = ''" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div v-if="saveInfo != ''" class="alert alert-success alert-dismissible fade show" role="alert">
                        <ul v-html="saveInfo"></ul>
                    <button type="button" class="close" v-on:click="saveInfo = ''" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div v-if="deleteInfo != ''" class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ deleteInfo }}
                    <button type="button" class="close" v-on:click="deleteInfo = ''" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div v-if="error != ''" class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul v-html="error"></ul>
                    <button type="button" class="close" v-on:click="error = ''" aria-label="Close">
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
                days: [],
                months: [
                    {id:1, name:'January'},
                    {id:2, name:'February'},
                    {id:3, name:'March'},
                    {id:4, name:'April'},
                    {id:5, name:'May'},
                    {id:6, name:'June'},
                    {id:7, name:'July'},
                    {id:8, name:'August'},
                    {id:9, name:'September'},
                    {id:10, name:'October'},
                    {id:11, name:'November'},
                    {id:12, name:'December'},
                ],
                years: [],
                date: {
                    from: {
                        day: null,
                        month: null,
                        year: null,
                    },
                    to: {
                        day: null,
                        month: null,
                        year: null,
                    }
                },
                time: {
                    from: {
                        hour: null,
                        minute: null,
                    },
                    to: {
                        hour: null,
                        minute: null,
                    },
                },
            };
        },
        created() {
            this.fetchData();
        },
        methods: {
            changeMonth() {
                this.days = [];
                let amount = 0;
                if((this.date.from.month % 2 == 0 && this.date.from.month < 8) || (this.date.from.month % 2 == 1 && this.date.from.month > 7)){
                    amount = 30;
                }
                if((this.date.from.month % 2 == 0 && this.date.from.month > 7) || (this.date.from.month % 2 == 1 && this.date.from.month < 8)){
                    amount = 31;
                }
                if(this.date.from.month==2){
                    //if is schrikkeljaar
                    amount = 28;
                    //if is not
                    amount = 27
                }
                for(let i = 1; i <= amount; i++) {
                    this.days.push(i);
                }
            },

            fillYears() {
                for(let i = this.date.from.year; i < (this.date.from.year + 5); i++) {
                    this.years.push(i);
                }
            },

            fetchData() {
                if(!this.edit){
                    let d = new Date();
                    this.date.from.day = this.date.to.day = d.getDate();
                    this.date.from.month = this.date.to.month = this.months[d.getMonth()].id;
                    this.date.from.year = this.date.to.year = d.getFullYear();
                    this.time.from.hour = d.getHours();
                    this.time.from.minute = d.getMinutes();
                    this.time.to.hour = d.getHours() + 1;
                    this.time.to.minute = d.getMinutes();
                    this.changeMonth();
                    this.fillYears();
                }
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
                        let dateSplitted = this.task.starts_at.split('-').join('T').split('T').join(':').split(':');
                        this.date.from.year = dateSplitted[0];
                        this.date.from.month = this.months[parseInt(dateSplitted[1]) -1].id;
                        this.date.from.day = dateSplitted[2];
                        this.time.from.hour = dateSplitted[3];
                        this.time.from.minute = dateSplitted[4];
                        dateSplitted = this.task.ends_at.split('-').join('T').split('T').join(':').split(':');
                        this.date.to.year = dateSplitted[0];
                        this.date.to.month = this.months[parseInt(dateSplitted[1]) -1].id;
                        this.date.to.day = dateSplitted[2];
                        this.time.to.hour = dateSplitted[3];
                        this.time.to.minute = dateSplitted[4];
                        this.loading = false;
                        let fileSplitted = this.task.attachment.split('.').join('/').split('/');
                        this.attachmentName = fileSplitted[fileSplitted.length-2];
                        if(typeof this.attachmentName != 'undefined'){
                            this.attachmentName = this.attachmentName.split('_').splice(1).join("_");
                            let fileExt = fileSplitted[fileSplitted.length-1];
                            if(this.imageTypes.indexOf(fileExt) != -1) {
                                this.isImage = true;
                            }
                        }
                    }).catch(error => {
                    this.loading = false;
                    this.displayError(error);
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
                let dateJoined = "";
                let timeJoined = "";
                let array = [];
                array.push(this.date.from.year);
                if(this.date.from.month > 9){
                    array.push(this.date.from.month);
                } else {
                    let corrected = "0" + this.date.from.month;
                    array.push(corrected);
                }
                array.push(this.date.from.day);
                dateJoined = array.join("-");
                array = [];
                array.push(this.time.from.hour);
                array.push(this.time.from.minute);
                timeJoined = array.join(":");
                timeJoined = timeJoined + ":00";
                this.task.starts_at = dateJoined + "T" + timeJoined;

                array = [];
                array.push(this.date.to.year);
                if(this.date.to.month > 9){
                    array.push(this.date.to.month);
                } else {
                    let corrected = "0" + this.date.to.month;
                    array.push(corrected);
                }
                array.push(this.date.to.day);
                dateJoined = array.join("-");
                array = [];
                array.push(this.time.to.hour);
                array.push(this.time.to.minute);
                timeJoined = array.join(":");
                timeJoined = timeJoined + ":00";
                this.task.ends_at = dateJoined + "T" + timeJoined;

                //TODO: Validate postdata
                this.validateInput();

                // Send data if everything is fine
                if(this.inputInfo == "") {
                    const formData = new FormData();
                    formData.set('title', this.task.title);
                    formData.set('description', this.task.description);
                    formData.set('starts_at', this.task.starts_at);
                    formData.set('ends_at', this.task.ends_at);
                    formData.set('_method', 'PATCH');
                    console.log("before");
                    if (typeof this.task.attachment.name != 'undefined') {
                        formData.append('attachment', this.task.attachment, this.task.attachment.name);
                    }
                    console.log('after');
                    axios.post('/api/task/' + this.task.id, formData)
                        .then(response => {
                            this.saveInfo = "Task saved successfully!"
                            console.log(this.date.to.month);
                            this.fetchData();
                        }).catch(error => {
                        this.loading = false;
                        this.displayError(error);
                    });
                }
            },

            // Save a new created task
            createTask() {
                //TODO: validate postdata
                let dateJoined = "";
                let timeJoined = "";
                let array = [];
                array.push(this.date.from.year);
                if(this.date.from.month > 9){
                    array.push(this.date.from.month);
                } else {
                    let corrected = "0" + this.date.from.month;
                    array.push(corrected);
                }
                array.push(this.date.from.day);
                dateJoined = array.join("-");
                array = [];
                array.push(this.time.from.hour);
                array.push(this.time.from.minute);
                timeJoined = array.join(":");
                timeJoined = timeJoined + ":00";
                this.task.starts_at = dateJoined + "T" + timeJoined;

                array = [];
                array.push(this.date.to.year);
                if(this.date.to.month > 9){
                    array.push(this.date.to.month);
                } else {
                    let corrected = "0" + this.date.to.month;
                    array.push(corrected);
                }
                array.push(this.date.to.day);
                dateJoined = array.join("-");
                array = [];
                array.push(this.time.to.hour);
                array.push(this.time.to.minute);
                timeJoined = array.join(":");
                timeJoined = timeJoined + ":00";
                this.task.ends_at = dateJoined + "T" + timeJoined;

                // Checking if everything is filled in
                this.validateInput();

                // Send data if everything is fine
                if(this.inputInfo == "") {
                    const formData = new FormData();
                    formData.append('title', this.task.title);
                    formData.append('description', this.task.description);
                    formData.append('starts_at', this.task.starts_at);
                    formData.append('ends_at', this.task.ends_at);
                    if(this.task.attachment != null) {
                        formData.append('attachment', this.task.attachment, this.task.attachment.name);
                    }
                    axios
                        .post('/api/task', formData)
                        .then(response => {
                            this.saveInfo = "New task created successfully!"
                            this.fetchData();
                        }).catch(error => {
                            this.loading = false;
                            this.displayError(error);
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
                        this.displayError(error);
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

            // Takes an axios error object and sets error HTML in the data.error property.
            displayError(error) {
                let message = error.response.data.message || error.message;
                    message = "<li style='list-style:none;'><strong>" + message + "</strong></li>";
                if (typeof error.response.data.errors != 'undefined') {
                    for (var i = 0; i < error.response.data.errors.length; i++) {
                        message += "<li>" + error.response.data.errors[i] + "</li>";
                    }
                }
                this.error = message;
            },


            validateInput() {
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
                    this.inputInfo += "<li>Start date must be before end date</li>"
                }
            },
        }
    }
</script>
