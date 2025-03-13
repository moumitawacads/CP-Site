<template>
  <section class="mt-4">
    <table class="table table-striped table-bordered mt-3">
      <tbody>
        <tr>
          <td>{{ $t("ai_control") }}</td>
          <td>
            <label class="switch" style="margin-left: 8px">
              <input type="checkbox" :checked="userData.system_settings.ai_control_flag === 1"
                @change="changesSystemSettings($event, userData.id, 'ai_control_flag')" />
              <span class="slider round"></span>
            </label>
          </td>
        </tr>
        <tr>
          <td>{{ $t("homework_helper") }}</td>
          <td>
            <label class="switch" style="margin-left: 8px">
              <input type="checkbox" :checked="userData.system_settings.homework_helper_flag === 1" @change="
                changesSystemSettings($event, userData.id, 'homework_helper_flag')
                " />
              <span class="slider round"></span>
            </label>
          </td>
        </tr>
        <tr>
          <td>{{ $t("pre_set_videos") }}</td>
          <td>
            <label class="switch" style="margin-left: 8px">
              <input type="checkbox" :checked="userData.system_settings.pre_set_videos_flag === 1" @change="
                changesSystemSettings($event, userData.id, 'pre_set_videos_flag')
                " />
              <span class="slider round"></span>
            </label>
          </td>
        </tr>
        <tr>
          <td>{{ $t("cues") }}</td>
          <td>
            <label class="switch" style="margin-left: 8px">
              <input type="checkbox" :checked="userData.system_settings.cues_flag === 1"
                @change="changesSystemSettings($event, userData.id, 'cues_flag')" />
              <span class="slider round"></span>
            </label>
          </td>
        </tr>
        <tr>
          <td>{{ $t("prompts") }}</td>
          <td>
            <label class="switch" style="margin-left: 8px">
              <input type="checkbox" :checked="userData.system_settings.prompts_flag === 1"
                @change="changesSystemSettings($event, userData.id, 'prompts_flag')" />
              <span class="slider round"></span>
            </label>
          </td>
        </tr>
        <tr>
          <td>{{ $t("zoom_mode_no_lagging_between_games") }}</td>
          <td>
            <label class="switch" style="margin-left: 8px">
              <input type="checkbox" :checked="userData.system_settings.zoom_mode_flag === 1"
                @change="changesSystemSettings($event, userData.id, 'zoom_mode_flag')" />
              <span class="slider round"></span>
            </label>
          </td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script>
import axios from "../../axios";
export default {
  props: ["userData"],
  data() {
    return {};
  },
  methods: {
    async changesSystemSettings(e, id, flag) {
      e.preventDefault();
      let flagValue = e.target.checked ? 1 : 0;
      try {
        const response = await axios.put(
          "/api/clinician/update-system-settings/" + id,
          {
            tab_id: "system-function",
            key: flag,
            value: flagValue,
          },
          {
            headers: {
              Authorization: "Bearer " + this.$store.state.token,
            },
          }
        );
        if (response && response.status == 200) {
          // console.log(response);
        }
      } catch (error) {
        console.error("Error uploading file:", error);
      }
    },
  },
};
</script>
<style scoped>
h1 {
  color: #4caf50;
}

.container {
  max-width: 600px;
  border: 2px solid rgb(0, 0, 0);
  border-radius: 8px;
  padding: 20px;
}

.form-input {
  transition: border-color 0.3s ease;
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 8px;
  width: 100%;
  margin-bottom: 10px;
}

.form-input:focus {
  border-color: #4f46e5;
  outline: none;
}

.err {
  color: red;
}
</style>
