<template>
  <div class="">
    <h2 class="mb-4">{{ this.$route.params.id ? "Edit What's" : "Add" }} New</h2>
    <div class="alert alert-success" role="alert" v-if="message">
      <span>{{ message }}</span>
    </div>
    <form class="needs-validation" novalidate ref="whatsNewForm"
      @submit.prevent="this.$route.params.id ? update($event, this.$route.params.id) : submit($event)">
      <div class="form-group">
        <label for="title">Title:</label>
        <input type="text" v-model="formData.title" class="form-control" id="title" placeholder="Enter Title"
          required />
        <div class="invalid-feedback">{{ errors.title }}</div>
      </div>
      <div class="form-group">
        <label for="content">Content:</label>
        <ckeditor :editor="editor" v-model="formData.content" :config="editorConfig" />
        <div class="invalid-feedback">{{ errors.content }}</div>
      </div>
      <div class="form-group">
        <label for="content">Status:</label>
        <select v-model="formData.status" class="form-select">
          <option value="pending">Pending</option>
          <option value="published">Published</option>
        </select>
      </div>
      <div class="form-group">
        <label for="content">Category:</label>
        <select v-model="formData.category_id" class="form-select" required>
          <option value="">Select Category</option>
          <option v-for="(category, indexCat) in categoryList" :key="indexCat + 1" :value="category.id">
            {{ category.name }}
          </option>
        </select>
        <div class="invalid-feedback">{{ errors.category_id }}</div>
      </div>
      <div class="form-group">
        <label for="file">Image/Video:</label>
        <input type="file" class="form-control" @change="handleFileUpload" />
      </div>
      <div class="form-group">
        <label for="preview">Preview:</label>
        <img v-if="filePreview && isImage" :src="filePreview" alt="image" class="preview-img" />
        <video v-if="filePreview && isVideo" :src="filePreview" controls class="preview-video"></video>
        <img v-if="!filePreview && !isImage" src="../../../../img/no-image.png" alt="image" class="preview-img" />
      </div>
      <div class="form-group mt-3">
        <button type="submit" class="btn btn-primary" :disabled="disabledBtn">
          Submit
        </button>
        <router-link to="/admin/whats-new" class="btn btn-secondary" :disabled="disabledBtn"
          style="margin-left: 8px">Back</router-link>
      </div>
    </form>
  </div>
</template>

<script>
import CKEditor from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import MyUploadAdapterPlugin from "../../../fileUpload"; // Import your custom upload adapter

export default {
  name: "WhatsNewForm",
  components: {
    ckeditor: CKEditor.component,
  },
  data() {
    return {
      formData: {
        title: "",
        content: "",
        status: "pending",
        file: "",
        category_id: "",
      },
      message: null,
      categoryList: null,
      filePreview: null,
      isImage: false,
      isVideo: false,
      editor: ClassicEditor,
      //   editorData: "",
      editorConfig: {
        newHeight: "300px",
        placeholder: "Enter content here...",
        required: true,
        // Optional CKEditor configuration
        toolbar: [
          "heading",
          "|",
          "bold",
          "italic",
          "link",
          "bulletedList",
          "numberedList",
          "|",
          "blockQuote",
          "insertTable",
          "mediaEmbed",
          "imageUpload",
          "undo",
          "redo",
          "|",
          "|",
        ],
        extraPlugins: [MyUploadAdapterPlugin],
      },
      disabledBtn: false,
      errorMsg: null,
      successMsg: null,
      errors: {
        title: "Title field is required.",
        content: "content field is required.",
        category_id: "Category field is required."
      },
    };
  },
  mounted() {
    if (this.$route.params.id) {
      this.getWhatsNew(this.$route.params.id);
    }
    this.getCategories();
  },
  methods: {
    async submit(e) {
      this.disabledBtn = true;
      let validation = this.checkForm(e);
      if (validation) {
        const formData = new FormData();
        formData.append("title", this.formData.title);
        formData.append("content", this.formData.content);
        formData.append("status", this.formData.status);
        formData.append("category_id", this.formData.category_id);
        if (this.formData.file) {
          formData.append("file", this.formData.file);
        }
        try {
          const response = await axios.post("/api/admin/whats-new/create", formData, {
            headers: {
              Authorization: `Bearer ${this.$store.state.token}`,
              "Content-Type": "multipart/form-data",
            },
          });
          this.message = response.data.message;
          this.disabledBtn = false;
          this.errorMsg = null;
          this.clearForm();
        } catch (error) {
          this.disabledBtn = false;
          this.errorMsg = error.response.data.message;
          console.error(error);
        }
      } else {
        this.disabledBtn = false;
      }
    },
    async update(e, id) {
      this.disabledBtn = true;
      let validation = this.checkForm(e);
      if (validation) {
        try {
          const formData = new FormData();
          formData.append("title", this.formData.title);
          formData.append("content", this.formData.content);
          formData.append("status", this.formData.status);
          formData.append("category_id", this.formData.category_id);
          if (this.formData.file) {
            formData.append("file", this.formData.file);
          }
          const response = await axios.post(`/api/admin/whats-new/update/${id}`, formData, {
            headers: {
              Authorization: `Bearer ${this.$store.state.token}`,
              "Content-Type": "multipart/form-data",
            },
          });
          this.message = response.data.message;
          this.disabledBtn = false;
          this.errorMsg = null;
          this.clearForm();
        } catch (error) {
          this.disabledBtn = false;
          this.errorMsg = error.response.data.message;
          console.error(error);
        }
      } else {
        this.disabledBtn = false;
      }
    },
    async getWhatsNew(id) {
      try {
        const response = await axios.get(`/api/admin/whats-new/edit/${id}`, {
          headers: {
            Authorization: `Bearer ${this.$store.state.token}`,
          },
        });
        this.formData = response.data.data;
        this.formData.content = JSON.parse(this.formData.content);
        this.filePreview = this.formData.file_url; // Set the file preview URL
        this.isImage =
          this.filePreview && this.filePreview.match(/\.(jpeg|jpg|gif|png|svg)$/) != null;
        this.isVideo =
          this.filePreview && this.filePreview.match(/\.(mp4|webm|ogg|mkv)$/) != null;
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
    handleFileUpload(event) {
      const file = event.target.files[0];
      this.formData.file = file; // Store the selected file
      this.filePreview = URL.createObjectURL(file); // Generate a preview URL
      this.isImage = file.type.startsWith("image/");
      this.isVideo = file.type.startsWith("video/");
    },
    checkForm(e) {
      if (this.formData.title != "" && this.formData.content != "" && this.formData.category_id != "") return true;
      let form = document.querySelector(".needs-validation");
      form.classList.add("was-validated");
      e.preventDefault();
      return false;
    },
    clearForm() {
      this.formData = {
        title: "",
        content: "",
        image: "",
        status: "pending",
      };
      this.filePreview = null;
      this.isImage = false;
      this.isVideo = false;
      setTimeout(() => {
        this.$router.push("/admin/whats-new");
      }, 2000);
    },
  },
};
</script>

<style scoped>
.custom-editor .ck-editor__editable {
  height: 500px !important;
  /* Adjust height */
}

.preview-img {
  display: block;
  width: 200px;
  height: 200px;
  border: 1px solid #333;
}

.preview-video {
  display: block;
  width: 350px;
  height: 200px;
  border: 1px solid #333;
}
</style>
