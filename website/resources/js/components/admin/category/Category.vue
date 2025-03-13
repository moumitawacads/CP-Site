<template>
  <div class="">
    <h2 class="mb-4">Category</h2>
    <div class="row">
      <div class="col-md-4">
        <input type="text" class="form-control mb-3" placeholder="Search..." v-model="searchQuery" />
      </div>
      <div class="col-md-4"></div>
      <div class="col-md-2"></div>
      <div class="col-md-2 d-flex justify-content-end">
        <router-link to="/admin/add-category" class="btn btn-primary mb-3">
          Add Category
        </router-link>
      </div>
    </div>

    <table class="table table-bordered" v-if="categoryList">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th style="width: 130px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <template v-if="filteredCategoryList.length === 0">
          <tr>
            <td colspan="3" class="text-center">No data found</td>
          </tr>
        </template>
        <template v-else>
          <tr v-for="(category, index) in filteredCategoryList" :key="category.id">
            <td>{{ index + 1 }}</td>
            <td>{{ category.name }}</td>
            <td>
              <button class="btn btn-primary btn-sm" @click="editCategory(category.id)">
                Edit
              </button>
              <button class="btn btn-danger btn-sm" @click="deleteCategory(category.id)" style="margin-left: 8px">
                Delete
              </button>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: "Category",
  data() {
    return {
      categoryList: null,
      searchQuery: "",
    };
  },
  computed: {
    filteredCategoryList() {
      return this.categoryList
        ? this.categoryList.filter((item) => {
          const matchesSearchQuery = item.name
            .toLowerCase()
            .includes(this.searchQuery.toLowerCase());
          return matchesSearchQuery;
        })
        : [];
    },
  },
  mounted() {
    this.getAllCategoryList();
  },
  methods: {
    async getAllCategoryList() {
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

    async editCategory(id) {
      this.$router.push({ name: "edit-category", params: { id } });
    },
    async deleteCategory(id) {
      if (confirm("Are you sure you want to delete this item?")) {
        try {
          const response = await axios.delete(`/api/admin/category/delete/${id}`, {
            headers: {
              Authorization: `Bearer ${this.$store.state.token}`,
            },
          });
          if (response && response.status === 200) {
            this.getAllCategoryList();
          }
        } catch (error) {
          console.error(error);
        }
      }
    },
  },
};
</script>

<style scoped></style>
