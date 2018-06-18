<button type="button" v-on:click="overview">Back</button>
<br />
<br />
Editing <b>@{{ currentTask.title }}</b>
<br />
<br />
<input v-model="currentTask.title"/>
<input v-model="currentTask.description"/>
<input v-model="currentTask.starts_at"/>
<input type="datetime-local" v-model="currentTask.ends_at"/>
