<template>
    <div class="tasks">
        <div class="loading" v-if="loading">
            Loading...
        </div>
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
        <ul v-if="tasks">
            <li v-for="{ title, description } in tasks">
                <strong>Title: </strong> {{ title }},
                <strong>Description: </strong> {{ description }}
            </li>
        </ul>
    </div>
</template>
<script>
    import axios from 'axios';
    export default {
        data() {
            return {
                loading: false,
                users: null,
                error: null,
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
            }
        }
    }
</script>
