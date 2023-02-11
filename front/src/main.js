import { createApp } from "vue";
import { createPinia } from "pinia";

import App from "./App.vue";
import router from "./router";

import { library } from "@fortawesome/fontawesome-svg-core";
import { faBars, faClose } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

import { SetupCalendar } from "v-calendar";

// Setup plugin for defaults or `$screens` (optional)

// Use plugin with defaults
library.add(faBars, faClose);

import "./assets/styles/style.scss";

const app = createApp(App);

app.use(SetupCalendar, {});
app.use(createPinia());
app.use(router);
app.component("FontAwesomeIcon", FontAwesomeIcon);

app.mount("#app");
