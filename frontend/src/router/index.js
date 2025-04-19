import { createRouter, createWebHistory } from "vue-router"
import FileUpload from "../components/FileUpload.vue"
import DownloadPage from "../components/DownloadPage.vue"

const routes = [
  {
    path: "/",
    name: "home",
    component: FileUpload,
  },
  {
    path: "/download/:filename",
    name: "download",
    component: DownloadPage,
    props: true,
  },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
  })
  

export default router

