<template>
  <div class="">
    <h2 class="mb-4">What's New</h2>
    <div class="row">
      <div class="col-md-3">
        <input type="text" class="form-control mb-3" placeholder="Search..." v-model="searchQuery" />
      </div>
      <div class="col-md-3" style="width: 22%"></div>
      <div class="col-md-3 d-flex align-items-baseline">
        <label for="category" style="margin-right: 12px">Category:</label>
        <select class="form-select mb-3" v-model="selectedCategory" @change="getAllWhatsNewList(1)">
          <option value="all">All</option>
          <option v-for="(category, indexCat) in categoryList" :key="indexCat + 1" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-baseline">
        <label for="status" style="margin-right: 12px">Status:</label>
        <select class="form-select mb-3" v-model="selectedStatus" @change="getAllWhatsNewList(1)">
          <option value="all">All</option>
          <option value="pending">Pending</option>
          <option value="published">Published</option>
        </select>
      </div>
      <div class="col-md-2 d-flex justify-content-end" style="width: 11%">
        <router-link to="/admin/add-whats-new" class="btn btn-primary mb-3">
          Add New
        </router-link>
      </div>
    </div>

    <table class="table table-bordered" v-if="whatsNewList">
      <thead>
        <tr>
          <!-- <th>ID</th> -->
          <th>Title</th>
          <th>Image/Video</th>
          <th style="width: 350px">Content</th>
          <th>Category</th>
          <!-- <th>Author</th> -->
          <th>Status</th>
          <th>Published At</th>
          <th style="width: 145px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <template v-if="filteredWhatsNewList.length === 0">
          <tr>
            <td colspan="7" class="text-center">No data found</td>
          </tr>
        </template>
        <template v-else>
          <tr v-for="whatsNew in filteredWhatsNewList" :key="whatsNew.id">
            <!-- <td>{{ index + 1 }}</td> -->
            <td>{{ whatsNew.title }}</td>
            <td>
              <div v-if="whatsNew && isImage(whatsNew.file_url)">
                <img :src="whatsNew.file_url" alt="image" class="preview-img" />
              </div>
              <div v-else-if="whatsNew && isVideo(whatsNew.file_url)">
                <video :src="whatsNew.file_url" controls class="preview-video"></video>
              </div>
            </td>
            <td>
              <div class="content-wrapper" v-html="getProcessedContent(whatsNew)"></div>
              <button class="btn btn-link p-0" @click="toggleExpand(whatsNew.id)">
                {{ isExpanded(whatsNew.id) ? "Show Less" : "Show More" }}
              </button>
            </td>
            <td>{{ whatsNew.category ? whatsNew.category.name : "" }}</td>
            <!-- <td>{{ whatsNew.author.fullname }}</td> -->
            <td>{{ whatsNew.status }}</td>
            <td class="text-center">
              {{ whatsNew.published_at ? formatDate(whatsNew.published_at) : "-" }}
            </td>
            <td>
              <button class="btn btn-primary btn-sm" @click="editWhatsNew(whatsNew.id)">
                Edit
              </button>
              <button class="btn btn-danger btn-sm" @click="deleteWhatsNew(whatsNew.id)" style="margin-left: 8px">
                Delete
              </button>
            </td>
          </tr>
        </template>
      </tbody>
    </table>

    <div class="row">
      <nav aria-label="Pagination" v-if="pagination.total && pagination.total > 20">
        <ul class="pagination justify-content-center">
          <li class="page-item" v-for="link in pagination.links" :key="link.label"
            :class="{ active: link.active, disabled: !link.url }">
            <a class="page-link" href="#" @click.prevent="link.url && getAllWhatsNewList(getPageNumber(link.url))"
              v-html="link.label === previousLabel || link.label === nextLabel
                ? $t(link.label)
                : link.label
                ">
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script>
import moment from "moment";

export default {
  name: "WhatsNew",
  data() {
    return {
      whatsNewList: null,
      searchQuery: "",
      selectedStatus: "all",
      selectedCategory: "all",
      expandedItems: [],
      categoryList: null,
      pagination: {
        current_page: 1,
        last_page: 1,
        links: [],
        data: [],
      },
      previousLabel: "&laquo; Previous",
      nextLabel: "Next &raquo;",
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
    this.getCategories();
  },
  methods: {
    async getAllWhatsNewList(page = 1) {
      try {
        const response = await axios.get(
          `/api/admin/whats-new?status=${this.selectedStatus}&category=${this.selectedCategory}&page=${page}`,
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
    async editWhatsNew(id) {
      this.$router.push({ name: "edit-whats-new", params: { id } });
    },
    async deleteWhatsNew(id) {
      if (confirm("Are you sure you want to delete this item?")) {
        try {
          const response = await axios.delete(`/api/admin/whats-new/delete/${id}`, {
            headers: {
              Authorization: `Bearer ${this.$store.state.token}`,
            },
          });
          if (response && response.status === 200) {
            this.getAllWhatsNewList();
          }
        } catch (error) {
          console.error(error);
        }
      }
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
        if (this.isExpanded(whatsNew.id)) {
          return whatsNew.processedContent;
        } else {
          return whatsNew.processedContent.substring(0, 200) + "...";
        }
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

.preview-img {
  display: block;
  width: 200px;
  height: 200px;
}

.preview-video {
  display: block;
  width: 250px;
  height: 150px;
}
</style>
