import { createStore } from 'vuex';
import SecureLS from 'secure-ls';
import axios from './axios';
import router from './router';
import moment from 'moment';

const ls = new SecureLS();

const store = createStore({
  state: {
    user: ls.get('user') || null,
    token: ls.get('token') || null,
    selectedLanguage: ls.get('lang') || 'en',
    cart: ls.get('cart') || [],
    providerData: ls.get("provider_data") || null,
    speechLanguageDiagnosisList: null,
    genders: [
      { value: "Male" },
      { value: "Female" },
      { value: "2SLGBTQ+" },
      { value: "Transgender" },
      { value: "Neurodivergent" }
    ],
    learnerAges: [
      { value: "3" }, { value: "4" }, { value: "5" }, { value: "6" },
      { value: "7" }, { value: "8" }, { value: "9" }, { value: "10" },
      { value: "11" }, { value: "12" }, { value: "13" }, { value: "14" },
      { value: "15" }, { value: "16" }, { value: "17" }, { value: "18" }
    ],
    grades: [
      { value: "Preschool" }, { value: "Junior Kindergarden" }, { value: "Senior Kindergarden" },
      { value: "Grade 1" }, { value: "Grade 2" }, { value: "Grade 3" }, { value: "Grade 4" },
      { value: "Grade 5" }, { value: "Grade 6" }, { value: "Grade 7" }, { value: "Grade 8" },
      { value: "Grade 9" }, { value: "Grade 10" }, { value: "Grade 11" }, { value: "Homeschool" },
      { value: "Not Applicable" }, { value: "Other" }
    ],
  },
  mutations: {
    setUser(state, user) {
      state.user = user;
      ls.set('user', user);
    },
    setToken(state, token) {
      state.token = token;
      ls.set('token', token);
      ls.set('token_expiry', Date.now() + 7 * 60 * 60 * 1000);
      ls.set('last_login_time', moment(new Date()).utc().format("YYYY-MM-DD HH:mm:ss"));
    },
    async setLanguage(state, selectedLanguage) {
      state.selectedLanguage = selectedLanguage;
      ls.set("lang", selectedLanguage);
    },
    setProviderData(state, providerData) {
      state.providerData = providerData;
      ls.set("provider_data", providerData);
    },
    setSpeechLanguageDiagnosisList(state, speechLanguageDiagnosisList) {
      state.speechLanguageDiagnosisList = speechLanguageDiagnosisList;
    },
    async clearAuth(state) {
      state.user = null;
      state.token = null;
      state.providerData = null;
      ls.removeAll();
      ls.remove("last_login_time");
      ls.remove("lang");
      ls.remove("token");
      ls.remove("token_expiry");
      ls.remove("user");
      ls.remove("responseData");
      ls.remove("provider_data");
      ls.remove("activeTab");
      const lang = "en";
      state.selectedLanguage = lang;
      ls.set("lang", lang);
    }
  },
  actions: {
    login({ commit }, payload) {
      commit('setUser', payload.user);
      commit('setToken', payload.user.access_token);
      commit('setLanguage', payload.user.preferred_language);
    },
    async logout({ commit, state }, payload = null) {
      if(payload) {
        commit('clearAuth');
        router.push({ name: "admin-login" });
      } else {
        const response = await axios.post("/api/logout", {
          user_id: state.user.id,
          login_time: ls.get("last_login_time"),
        },
        {
          headers: {
            Authorization: "Bearer " + state.token,
          },
        });
        if (response && response.status === 200) {
          commit('clearAuth');
          router.push({ name: "login" });
        }
      }
    },
    async getProviderData({commit, state}, providerType) {
      let url;
      if(providerType === 'clinician') {
        url = "/api/clinician/?user_id=" + state.user.id
      } else {
        url = "/api/parents/?user_id=" + state.user.id
      }
      const response = await axios.get(url,
          {
            headers: {
              Authorization: "Bearer " + state.token,
            },
          }
        );
        if (response && response.status == 200) {
          commit('setProviderData', response.data.data[0])
        }
    },
    async getSpeechLanguageDiagnosisList({commit}) {
      const response = await axios.get("/api/speech-language-diagnosis-list");
      if (response && response.status == 200) {
        commit('setSpeechLanguageDiagnosisList', response.data.data)
      }
    }
  },
  getters: {
    isAuthenticated: (state) => !!state.token,
    getUser: (state) => state.user,
    getCart: (state) => state.cart
  },
});

export default store;
