import { createRouter, createWebHistory } from "vue-router";

const routes = [
  {
    path: "/",
    name: "home",
    component: () => import("../views/HomeView.vue"),
    meta: {
      requiresAuth: false,
      roles: [],
    },
  },
  {
    path: "/housing/:slug",
    name: "housing",
    component: () => import("../views/HousingView.vue"),
    meta: {
      requiresAuth: true,
      roles: [],
    },
    props: (route) => ({
      slug: route.params.slug,
    }),
  },
  {
    path: "/about",
    name: "about",
    component: () => import("../views/AboutView.vue"),
    meta: {
      requiresAuth: false,
      roles: [],
    },
  },
  {
    path: "/result/:category",
    name: "result",
    component: () => import("../views/ResultView.vue"),
    meta: {
      requiresAuth: true,
      roles: [],
    },
  },
  {
    path: "/profile/renting",
    name: "profile-renting",
    component: () => import("../views/profile/RentingView.vue"),
    meta: {
      requiresAuth: true,
      roles: [],
    },
  },
  {
    path: "/profile/housing",
    name: "profile-housing",
    component: () => import("../views/profile/HousingView.vue"),
    meta: {
      requiresAuth: true,
      roles: [],
    },
  },
  {
    path: "/profile",
    name: "profile",
    component: () => import("../views/ProfileView.vue"),
    meta: {
      requiresAuth: true,
      roles: [],
    },
  },
  {
    path: "/login",
    name: "login",
    component: () => import("../views/LoginView.vue"),
  },
  {
    path: "/forgot-password",
    name: "forgot-password",
    component: () => import("../views/ForgotPasswordView.vue"),
  },
  {
    path: "/forgot-password/:token",
    name: "forgot-password-token",
    component: () => import("../views/ForgotPasswordTokenView.vue"),
    props: (route) => ({
      token: route.params.token,
    })
  },
  {
    path: "/housing/update/:slug",
    name: "housing_update",
    component: () => import("../views/profile/UpdateHousingView.vue"),
    meta: {
      requiresAuth: true,
      roles: [],
    },
    props: (route) => ({
      slug: route.params.slug,
    }),
  },
  {
    path: "/profile/housing/create",
    name: "profile-housing-create",
    component: () => import("../views/profile/CreateHousingView.vue"),
    meta: {
      requiresAuth: true,
      roles: [],
    },
  },
  {
    path: "/register",
    name: "register",
    component: () => import("../views/RegistrationView.vue"),
  },
  {
    path: "/confirm-account/:token",
    name: "confirm-account",
    component: () => import("../views/ConfirmAccountView.vue"),
    props: (route) => ({
      token: route.params.token,
    })
  },
  {
    path: "/profile/likes",
    name: "profile-likes",
    component: () => import("../views/profile/HousingLikedView.vue"),
    meta: {
      requiresAuth: true,
      roles: [],
    },
  },
  {
    path: "/admin/reports",
    name: "reports-admin",
    component: () => import("../views/admin/AdminReport.vue"),
    meta: {
      requiresAuth: true,
      roles: ['ROLE_ADMIN'],
    },
  }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: routes,
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token");

  if (to.matched.some((record) => record.meta.requiresAuth)) {
    if (!token) {
      next({
        path: "/login",
        query: {
          redirect: to.fullPath,
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
