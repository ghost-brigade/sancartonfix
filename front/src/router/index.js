import { createRouter, createWebHistory } from "vue-router";

const routes = [
  {
    path: "/",
    name: "home",
    component: () => import("../views/HomeView.vue"),
    meta: {
      requiresAuth: false,
      roles: [],
    }
  },
  {
    path: "/about",
    name: "about",
    component: () => import("../views/AboutView.vue"),
    meta: {
      requiresAuth: false,
      roles: [],
    }
  },
  {
    path: "/profile/renting",
    name: "profile-renting",
    component: () => import("../views/profile/RentingView.vue"),
    meta: {
      requiresAuth: true,
      roles: [],
    }
  },
  {
    path: "/profile/housing",
    name: "profile-housing",
    component: () => import("../views/profile/HousingView.vue"),
    meta: {
      requiresAuth: true,
      roles: [],
    }
  },
  {
    path: "/profile/housing/:id/edit",
    name: "profile-housing-edit",
    component: () => import("../views/profile/HousingEditView.vue"),
    meta: {
      requiresAuth: true,
      roles: [],
    }
  },
  {
    path: "/profile",
    name: "profile",
    component: () => import("../views/profile/ProfileView.vue"),
    meta: {
      requiresAuth: true,
      roles: [],
    }
  },
  {
    path: "/login",
    name: "login",
    component: () => import("../views/LoginView.vue"),
  }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: routes
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token");

  if (to.matched.some((record) => record.meta.requiresAuth)) {
    if (!token) {
      next({
        path: "/login",
        query: {
          redirect: to.fullPath
        },
      });
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;
