<template>
  <div class="container">
    <template v-if="whatsNewList">
      <div class="row">
        <div class="col-md-3">
          <input type="text" class="form-control mb-3" placeholder="Search..." v-model="searchQuery" />
        </div>
      </div>
      <div class="row" v-if="filteredWhatsNewList.length > 0">
        <div class="col-md-12 mb-3" v-for="whatsNew in filteredWhatsNewList" :key="whatsNew.id">
          <div class="accordion p-12" id="accordionFaq">
            <div class="accordion-item">
              <h2 class="accordion-header" :id="'faqDetails' + whatsNew.id">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  :data-bs-target="'#collapseTwo' + whatsNew.id" aria-expanded="false"
                  :aria-controls="'collapseTwo' + whatsNew.id">
                  <b>{{ whatsNew.title }}</b>
                </button>
              </h2>
              <div :id="'collapseTwo' + whatsNew.id" class="accordion-collapse collapse"
                :aria-labelledby="'faqDetails' + whatsNew.id" data-bs-parent="#accordionFaq">
                <div class="accordion-body">
                  <p class="card-text content-wrapper" v-html="getProcessedContent(whatsNew)"></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-center" v-else>
        <p>No record found!</p>
      </div>
      <!-- <div class="row">
        <nav aria-label="Pagination" v-if="pagination.total && pagination.total > 20">
          <ul class="pagination justify-content-center">
            <li
              class="page-item"
              v-for="link in pagination.links"
              :key="link.label"
              :class="{ active: link.active, disabled: !link.url }"
            >
              <a
                class="page-link"
                href="#"
                @click.prevent="link.url && getWhatsNew(getPageNumber(link.url))"
                v-html="
                  link.label === previousLabel || link.label === nextLabel
                    ? $t(link.label)
                    : link.label
                "
              >
              </a>
            </li>
          </ul>
        </nav>
      </div> -->
    </template>
  </div>
  <FullImageOrVideoModal :showModal="showModal" :modalContent="modalContent" @closeModal="closeModal" />
</template>

<script>
import moment from "moment";
import FullImageOrVideoModal from "../components/common/FullImageOrVideoModal.vue";

export default {
  name: "FAQ",
  components: {
    FullImageOrVideoModal,
  },
  data() {
    return {
      whatsNewList: null,
      searchQuery: "",
      expandedItems: [],
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
    this.getAllWhatsNewList();
  },
  methods: {
    async getAllWhatsNewList(page = 1) {
      try {
        const response = await axios.get(
          `/api/admin/whats-new?status=published&category_name=faq&page=${page}`,
          {
            headers: {
              Authorization: `Bearer ${this.$store.state.token}`,
            },
          }
        );
        this.whatsNewList = response.data.data;
        this.pagination = response.data.meta;
        this.processContent();
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
      let stillUtc = moment.utc(date).toDate();
      //moment(date).format("MMM DD, YYYY");
      return moment(stillUtc).local().format("MMM DD, YYYY");
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
      if (whatsNew.processedContent) {
        // if (this.isExpanded(whatsNew.id)) {
        return whatsNew.processedContent;
        //   } else {
        //     return whatsNew.processedContent.substring(0, 100) + "...";
        //   }
        // } else {
        //   return whatsNew.processedContent;
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
</style>
