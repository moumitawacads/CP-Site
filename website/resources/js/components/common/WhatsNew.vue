<template>
  <div class="mt-4">
    <template v-if="whatsNewList">
      <div class="row">
        <div class="col-md-3">
          <input type="text" class="form-control mb-3" placeholder="Search..." v-model="searchQuery" />
        </div>
        <div class="col-md-7"></div>
        <div class="col-md-2">
          <label for="category" style="margin-right: 12px">Category:</label>
          <select class="form-select mb-3" v-model="selectedCategory" @change="getWhatsNew(1)">
            <option value="all">All</option>
            <option v-for="(category, indexCat) in categoryList" :key="indexCat + 1" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
      </div>
      <div class="row" v-if="filteredWhatsNewList.length > 0">
        <div class="col-md-6 mb-4" v-for="whatsNew in filteredWhatsNewList" :key="whatsNew.id">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                  <div v-if="isImage(whatsNew.file_url)" @click="openModal(whatsNew)">
                    <img :src="whatsNew.file_url" alt="image" class="preview-img" />
                  </div>
                  <div v-else-if="isVideo(whatsNew.file_url)" @click="openModal(whatsNew)">
                    <video :src="whatsNew.file_url" controls class="preview-video"></video>
                  </div>
                </div>
                <div class="col-md-9">
                  <h5 class="card-title">{{ whatsNew.title }}</h5>
                  <h6 class="card-subtitle text-muted subtitle">
                    Published At: {{ formatDate(whatsNew.published_at) }}
                  </h6>
                  <p class="card-text content-wrapper" v-html="getProcessedContent(whatsNew)"></p>
                  <div class="d-flex justify-content-end" v-if="
                    whatsNew.processedContent && whatsNew.processedContent.length > 100
                  ">
                    <!-- <button class="btn btn-link p-0" @click="toggleExpand(whatsNew.id)">
                      {{ isExpanded(whatsNew.id) ? "Show Less" : "Show More" }}
                    </button> -->
                    <button class="btn btn-link p-0" @click="openModal(whatsNew)">
                      Show More
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-center" v-else>
        <p>No record found!</p>
      </div>
      <div class="row">
        <nav aria-label="Pagination" v-if="pagination.total && pagination.total > 20">
          <ul class="pagination justify-content-center">
            <li class="page-item" v-for="link in pagination.links" :key="link.label"
              :class="{ active: link.active, disabled: !link.url }">
              <a class="page-link" href="#" @click.prevent="link.url && getWhatsNew(getPageNumber(link.url))" v-html="link.label === previousLabel || link.label === nextLabel
                ? $t(link.label)
                : link.label
                ">
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </template>
  </div>
  <FullImageOrVideoModal :showModal="showModal" :modalContent="modalContent" @closeModal="closeModal" />
</template>

<script>
import moment from "moment";
import axios from "../../axios";
import FullImageOrVideoModal from "./FullImageOrVideoModal.vue";
export default {
  name: "WhatsNew",
  props: ["userData"],
  emits: ["removeNewLearnerId", "navigate"],
  components: {
    FullImageOrVideoModal,
  },
  data() {
    return {
      whatsNewList: null,
      errorMsg: null,
      expandedItems: [],
      categoryList: null,
      searchQuery: "",
      selectedCategory: "all",
      pagination: {
        current_page: 1,
        last_page: 1,
        links: [],
        data: [],
      },
      previousLabel: "&laquo; Previous",
      nextLabel: "Next &raquo;",
      showModal: false,
      modalContent: null,
    };
  },
  computed: {
    filteredWhatsNewList() {
      return this.whatsNewList
        ? this.whatsNewList.filter((item) => {
          const matchesSearchQuery =
            item.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            item.content.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            item.author.fullname.toLowerCase().includes(this.searchQuery.toLowerCase());
          return matchesSearchQuery;
        })
        : [];
    },
  },
  mounted() {
    this.getWhatsNew();
    this.getCategories();
  },
  methods: {
    async getWhatsNew(page = 1) {
      await axios
        .get(
          `/api/admin/whats-new?status=published&category=${this.selectedCategory}&page=${page}`,
          {
            headers: {
              Authorization: `Bearer ${this.$store.state.token}`,
            },
          }
        )
        .then((response) => {
          this.whatsNewList = response.data.data;
          this.pagination = response.data.meta;
          this.processContent();
        })
        .catch((error) => {
          this.errorMsg = error.response.data.message;
        });
    },
    async getCategories() {
      try {
        const response = await axios.get("/api/admin/category", {
          headers: {
            Authorization: `Bearer ${this.$store.state.token}`,
          },
        });
        this.categoryList = response.data.data;
      } catch (error) {
        console.error(error);
      }
    },
    async processContent() {
      for (let item of this.whatsNewList) {
        item.processedContent = await this.getContent(item.content);
      }
    },
    async getContent(content) {
      try {
        const parsedContent = JSON.parse(content);
        return await this.replaceOembedTags(parsedContent);
      } catch (e) {
        return await this.replaceOembedTags(content);
      }
    },
    async replaceOembedTags(content) {
      return content.replace(/<oembed url="(.+?)".*?><\/oembed>/g, (match, url) => {
        const videoId = url.split("v=")[1];
        const embedUrl = `https://www.youtube.com/embed/${videoId}`;
        return `<iframe width="360" height="315" src="${embedUrl}" frameborder="0" allowfullscreen></iframe>`;
      });
    },
    formatDate(date) {
      return moment(date).format("MMM DD, YYYY");
    },
    toggleExpand(id) {
      if (this.expandedItems.includes(id)) {
        this.expandedItems = this.expandedItems.filter((item) => item !== id);
      } else {
        this.expandedItems.push(id);
      }
    },
    isExpanded(id) {
      return this.expandedItems.includes(id);
    },
    getProcessedContent(whatsNew) {
      if (whatsNew.processedContent && whatsNew.processedContent.length > 100) {
        if (this.isExpanded(whatsNew.id)) {
          return whatsNew.processedContent;
        } else {
          return whatsNew.processedContent.substring(0, 100) + "...";
        }
      } else {
        return whatsNew.processedContent;
      }
    },
    isImage(url) {
      return url ? url.match(/\.(jpeg|jpg|gif|png|svg)$/) != null : false;
    },
    isVideo(url) {
      return url ? url.match(/\.(mp4|webm|ogg|mkv)$/) != null : false;
    },
    getPageNumber(url) {
      const params = new URLSearchParams(url.split("?")[1]);
      return params.get("page");
    },
    openModal(content) {
      this.modalContent = content;
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.modalContent = null;
    },
  },
};
</script>

<style scoped>
h1 {
  color: #4caf50;
}

.err {
  color: red;
}

.table .content-wrapper img,
.table .content-wrapper iframe {
  max-width: 100%;
  height: auto;
}

.embed-container {
  position: relative;
  padding-bottom: 56.25%;
  height: 0;
  overflow: hidden;
  max-width: 100%;
  background: #000;
}

.embed-container iframe,
.embed-container object,
.embed-container embed {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.subtitle {
  font-size: 13px;
  font-style: italic;
  margin-bottom: 20px;
}

.btn-link {
  text-decoration: none;
}

.preview-img,
.preview-video {
  display: block;
  width: 100px;
  height: 100px;
  border-radius: 50%;
  cursor: pointer;
}

/* .preview-video {
  display: block;
  width: 50px;
  height: 50px;
  border-radius: 50%;
} */
</style>
