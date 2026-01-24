import { createRouter, createWebHistory } from "vue-router"
import Home from "../pages/Home.vue"
import Login from "../pages/Login.vue"
import Signup from "../pages/Signup.vue"
import FileUpload from "../components/FileUpload.vue"
import DownloadPage from "../components/DownloadPage.vue"

const routes = [
  {
    path: "/",
    name: "home",
    component: Home,
  },
  {
    path: "/upload",
    name: "upload",
    component: FileUpload,
  },
  {
    path: "/download/:filename",
    name: "download",
    component: DownloadPage,
    props: true,
  },
  {
    path: "/login",
    name: "login",
    component: Login,
    meta: { layout: "auth" },
  },
  {
    path: "/signup",
    name: "signup",
    component: Signup,
    meta: { layout: "auth" },
  },
  {
    path: "/:pathMatch(.*)*",
    name: "not-found",
    component: Home,
  },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior() {
      return { top: 0 }
    },
  })
  

export default router

