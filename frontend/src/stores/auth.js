import { defineStore } from 'pinia'


export const authStore = defineStore('auth', {
  state: () => {
    return {
      token: '',
      authenticated: false,
      api: '',
      roles: [],
      permissions: [],
      user: Object,
      navs: [],
      theme: {
        sidebar: {
          open: true,
          visible: true,
          src: "./src/assets/images/sidebar/sidebar1.jpeg"
        },
        footer: {
          src: "./src/assets/images/footer.png"
        },
        color: {
          name: "blue",
          class: "bg-blue-700",
          gradient: "bg-gradient-to-r from-blue-700 to-blue-900",
          hover: "hover:bg-blue-600",
          link: "text-blue-700",
          ring: "ring-blue-700",
          input: "bg-blue-50"

        }
      },
    }
  },
  persist: true,
  // could also be defined as
  // state: () => ({ count: 0 })
  actions: {
    setSession(data) {
      this.user = data.user
      this.token = data.token;
      this.authenticated = true;
      this.navs = data.user.navs
      this.permissions = data.user.permissions
    },
    setApi() {
      this.api = import.meta.env.VITE_API_URL
    },
    setTheme(t) {
      this.theme.color = t
    },
    collapseSidebar() {
      this.theme.sidebar.open = !this.theme.sidebar.open
    },
    hideSidebar() {
      this.theme.sidebar.visible = !this.theme.sidebar.visible
    },
    changeSrc(src) {
      this.theme.sidebar.src = src
    },
    logout() {
      this.token = null
      this.authenticated = false
      this.roles = []
      this.permissions = []
      this.user = new Object
      this.navs = []
    },
  },
})
