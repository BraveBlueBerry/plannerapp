<button type="button" v-on:click="overview">Back</button>
<br />
<br />
Editing <b>@{{ currentTask.title }}</b>
<br />
<br />
<input v-model="currentTask.description"/>
