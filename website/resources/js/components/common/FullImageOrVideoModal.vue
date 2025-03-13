<template>
  <div v-if="showModal" class="modal-backdrop" @click="closeModal"></div>
  <div v-if="showModal" class="modal d-block">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="card-title">{{ modalContent.title }}</h5>
          <button class="btn btn-close" @click="closeModal"></button>
        </div>
        <div class="modal-body">
          <div class="text-center">
            <img
              v-if="isImage(modalContent.file_url)"
              :src="modalContent.file_url"
              alt="image"
              class="full-img"
            />
            <video
              v-if="isVideo(modalContent.file_url)"
              :src="modalContent.file_url"
              controls
              class="full-video"
            ></video>
          </div>
          <p
            class="card-text content-wrapper mt-4"
            v-html="getProcessedContent(modalContent)"
          ></p>
          <h6 class="card-subtitle text-muted subtitle mt-4 nav justify-content-end">
            Published At: {{ formatDate(modalContent.published_at) }}
          </h6>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";

export default {
  name: "FullImageOrVideoModal",
  props: ["showModal", "modalContent"],
  emits: ["closeModal"],
  methods: {
    isImage(url) {
      return url ? url.match(/\.(jpeg|jpg|gif|png|svg)$/) != null : false;
    },
    isVideo(url) {
      return url ? url.match(/\.(mp4|webm|ogg|mkv)$/) != null : false;
    },
    closeModal() {
      this.$emit("closeModal");
    },
    getProcessedContent(whatsNew) {
      if (whatsNew.processedContent) {
        return whatsNew.processedContent;
      }
    },
    formatDate(date) {
      return moment(date).format("MMM DD, YYYY");
    },
  },
};
</script>

<style scoped>
.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5);
  /* Adjust opacity if needed */
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

/* .modal-content {
    position: relative;
    background: #fff;
    padding: 30px 20px 20px 20px;
    border-radius: 8px;
    max-width: 90%;
    max-height: 90%;
    overflow: auto;
} */

.close-button {
  position: absolute;
  top: -8px;
  right: 0px;
  background: none;
  border: none;
  font-size: 32px;
  cursor: pointer;
}

.full-img {
  max-width: 100%;
  height: auto;
}

.full-video {
  max-width: 100%;
  height: auto;
}

.subtitle {
  font-size: 13px;
  font-style: italic;
  margin-bottom: 20px;
}
</style>
