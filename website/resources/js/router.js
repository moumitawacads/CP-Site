import { createRouter, createWebHistory } from 'vue-router';
import store from './store';
import SecureLS from 'secure-ls';

const routes = [
  { path: '/', component: () => import('./pages/Home.vue'), name: 'home' },
  { path: '/:pathMatch(.*)*', component: () => import('./pages/NotFound.vue'), name: '404-not-found' },
  { path: '/clinician-register', component: () => import('./pages/ClinicianRegister.vue'), name: 'clinician-register' },
  { path: '/learner-register', component: () => import('./pages/LearnerRegister.vue'), name: 'learner-register' },
  { path: '/parent-register', component: () => import('./pages/ParentRegister.vue'), name: 'parent-register' },
  { path: '/shopping-cart', component: () => import('./pages/ShoppingCart.vue'), name: 'shopping-cart' },
  { path: '/checkout', component: () => import('./pages/Checkout.vue'), name: 'checkout' },
  { path: '/choose-learner', component: () => import('./pages/ChooseLearner.vue'), name: 'choose-learner' },
  { path: '/calendar', component: () => import('./components/Calendar.vue'), name: 'calendar' },
  { path: '/scheduled-meeting', component: () => import('./components/ScheduledMeeting.vue'), name: 'scheduled-meeting' },
  { path: '/price', component: () => import('./pages/Price.vue'), name: 'price' },
  { path: '/faq', component: () => import('./pages/FAQ.vue'), name: 'faq' },
  { path: '/login', component: () => import('./pages/Login.vue'), name: 'login' },
  { path: '/verification', component: () => import('./components/Verification.vue'), name: 'verification' },
  { path: '/dashboard', component: () => import('./pages/Dashboard.vue'), name: 'dashboard', meta: { requiresAuth: true } },
  { path: '/learner-dashboard/:id', component: () => import('./components/learners/LearnerDashboard.vue'), name: 'learner-dashboard', meta: { requiresAuth: true } },
  { path: '/admin-login', component: () => import('./pages/Login.vue'), name: 'admin-login' },
  {
    path: '/admin',
    component: () => import('./components/admin/AdminDashboard.vue'),
    name: 'admin-dashboard',
    meta: { requiresAdmin: true },
    children: [
      {
        path: 'services',
        name: 'services',
        children: [
          {
            path: 'assessments',
            component: () => import('./components/admin/assessment/Assessments.vue'),
            name: 'assessments',
          },
          {
            path: 'assign-assessment/:id',
            component: () => import('./components/admin/assessment/AssignAssessment.vue'),
            name: 'assign-assessment',
          },
          {
            path: 'literacy-diagnostics',
            component: () => import('./components/admin/literacy/LiteracyDiagnostics.vue'),
            name: 'literacy-diagnostics',
          },
          {
            path: 'assign-literacy-diagnostic/:id',
            component: () => import('./components/admin/literacy/AssignLiteracyDiagnostic.vue'),
            name: 'assign-literacy-diagnostic',
          },
        ],
      },
      {
        path: 'homework-helpers',
        component: () => import('./components/admin/homework-helper/HomeworkHelpers.vue'),
        name: 'homework-helpers',
      },
      {
        path: 'assign-homework-helper/:id',
        component: () => import('./components/admin/homework-helper/AssignHomeworkHelper.vue'),
        name: 'assign-homework-helper',
      },
      {
        path: 'clinicians',
        component: () => import('./components/admin/clinician/Clinician.vue'),
        name: 'clinicians',
      },
      {
        path: 'view-clinician-learners/:id',
        component: () => import('./components/admin/clinician/ViewLearners.vue'),
        name: 'view-clinician-learners',
      },
      {
        path: 'parents',
        component: () => import('./components/admin/parents/Parents.vue'),
        name: 'parents',
      },
      {
        path: 'view-parents-learners/:id',
        component: () => import('./components/admin/parents/ViewLearners.vue'),
        name: 'view-parents-learners',
      },
      {
        path: 'whats-new',
        component: () => import('./components/admin/whats-new/WhatsNew.vue'),
        name: 'whats-new',
      },
      {
        path: 'add-whats-new',
        component: () => import('./components/admin/whats-new/WhatsNewForm.vue'),
        name: 'add-whats-new',
      },
      {
        path: 'edit-whats-new/:id',
        component: () => import('./components/admin/whats-new/WhatsNewForm.vue'),
        name: 'edit-whats-new',
      },
      {
        path: 'category',
        component: () => import('./components/admin/category/Category.vue'),
        name: 'category',
      },
      {
        path: 'add-category',
        component: () => import('./components/admin/category/CategoryForm.vue'),
        name: 'add-category',
      },
      {
        path: 'edit-category/:id',
        component: () => import('./components/admin/category/CategoryForm.vue'),
        name: 'edit-category',
      }
    ],
  },
  
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const ls = new SecureLS();
  const isAuthenticated = ls.get('token');
  const tokenExpiry = ls.get('token_expiry');
  const authenticatePages = ['home','login', 'clinician-register', 'learner-register-by-clinician','learner-register', 'learner-register-by-parent', 'parent-register', 'verification','checkout','faq', 'admin-login'];
  const user = store.state.user;
  
  // Check if the token is expired
  const isTokenValid = isAuthenticated && tokenExpiry && Date.now() <= tokenExpiry;

  // console.log(isTokenValid, to.meta.requiresAuth)
  if (!isTokenValid) {
    if (to.meta.requiresAuth) {
      logout();
      return next({ name: 'login' });
    } else if (to.meta.requiresAdmin) {
      return next({ name: 'admin-login' });
    }
    return next();
  }

  // Restrict admin users from accessing normal user pages
  if (user && user.role_slug === 'administrator') {
    if (authenticatePages.includes(to.name) || !to.meta.requiresAdmin) {
      return next({ name: 'admin-dashboard' });
    }
    return next();
  }

  // Restrict normal users from accessing admin pages
  if (to.meta.requiresAdmin && (!user || user.role_slug !== 'administrator')) {
    return next({ name: 'login' });
  }

  // Redirect authenticated users from login/register pages to their respective dashboards
  if (authenticatePages.includes(to.name) && isAuthenticated) {
    return next(user.role_slug === 'administrator' ? { name: 'admin-dashboard' } : { name: 'dashboard' });
  }

  next();
});

function logout(value = null) {
  store.dispatch("logout", value);
}

export default router;