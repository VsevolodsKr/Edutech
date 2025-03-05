<template>
  <div>
    <Admin_Header />
    <section :class="[
      (showSidebar && width > 1180) ? 'pl-[22rem]' : 'pl-[2rem]',
      'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
    ]">
      <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-button"></div>
      </div>
      <template v-else>
        <div class="flex justify-between items-center mb-4">
          <h1 class="text-[1.5rem] text-text_dark capitalize">Your contents</h1>
          <span class="text-text_light">Total: {{ contents.length }}</span>
        </div>
        <hr class="border-[#ccc] mb-[2rem] mr-[1rem] [@media(max-width:550px)]:mr-[.5rem]">
        
        <div v-if="error" class="bg-[#fcb6b6] text-[#912020] p-4 rounded-lg mb-4">
          {{ error }}
        </div>

        <div class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem] justify-center items-start pr-[1rem] [@media(max-width:550px)]:flex [@media(max-width:550px)]:flex-col [@media(max-width:550px)]:pr-0">
          <div class="bg-base rounded-lg p-[2rem] w-full">
            <h3 class="text-[2rem] text-text_dark text-center capitalize pb-[.5rem] [@media(max-width:550px)]:text-[1.5rem]">Create new content</h3>
            <router-link 
              to="/admin_add_content" 
              class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
            >
              Add Content
            </router-link>
          </div>

          <TransitionGroup 
            name="list" 
            tag="div" 
            class="grid grid-cols-[repeat(auto-fit,_minmax(30rem,_1fr))] gap-[1rem]"
          >
            <div 
              v-for="content in contents" 
              :key="content.id" 
              class="bg-base rounded-lg p-[2rem] w-full"
            >
              <div class="flex items-center gap-[1.5rem] mb-[2rem] justify-between">
                <div>
                  <i :class="[
                    content.status === 'active' ? 'text-[#0eed46]' : 'text-[#e83731]',
                    'fas fa-circle-dot text-[1.2rem]'
                  ]"></i>
                  <span :class="[
                    content.status === 'active' ? 'text-[#0eed46]' : 'text-[#e83731]',
                    'text-[1.2rem] ml-2'
                  ]">
                    {{ content.status }}
                  </span>
                </div>
                <div>
                  <i class="fas fa-calendar text-button text-[1.2rem]"></i>
                  <span class="text-[1rem] text-text_light [@media(max-width:550px)]:text-[.7rem] ml-2">
                    {{ formatDate(content.date) }}
                  </span>
                </div>
              </div>

              <div class="relative group">
                <img 
                  :src="content.thumb" 
                  :alt="content.title"
                  class="w-full h-[20rem] object-cover rounded-lg [@media(max-width:550px)]:h-[12rem]"
                >
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 rounded-lg"></div>
              </div>

              <h3 class="text-[1.5rem] text-text_dark pb-[.5rem] pt-[1rem] [@media(max-width:550px)]:text-[1.2rem] line-clamp-2">
                {{ content.title }}
              </h3>

              <div class="flex justify-between w-full gap-[1rem] mb-[1rem]">
                <button 
                  @click="navigateToUpdate(content.id)"
                  :disabled="isDeleting"
                  class="bg-button2 text-base text-center border-2 border-button2 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button2 hover:bg-base disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                >
                  Update
                </button>
                <button 
                  @click="deleteContent(content.id)"
                  :disabled="isDeleting"
                  class="bg-button4 text-base text-center border-2 border-button4 rounded-lg py-[.5rem] block w-1/2 transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button4 hover:bg-base disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
                >
                  <span v-if="isDeletingId === content.id" class="inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Deleting...
                  </span>
                  <span v-else>Delete</span>
                </button>
              </div>

              <button 
                @click="navigateToView(content.id)"
                :disabled="isDeleting"
                class="bg-button text-base text-center border-2 border-button rounded-lg py-[.5rem] block w-full transition ease-linear duration-200 hover:transition hover:ease-linear hover:duration-200 hover:text-button hover:bg-base disabled:opacity-50 disabled:cursor-not-allowed [@media(max-width:550px)]:text-[.8rem] [@media(max-width:550px)]:py-[.2rem]"
              >
                View Content
              </button>
            </div>
          </TransitionGroup>
        </div>
      </template>
    </section>
    <Admin_Sidebar />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useWindowSize } from '@vueuse/core'
import Swal from 'sweetalert2'
import Admin_Header from '../components/Admin_Header.vue'
import Admin_Sidebar from '../components/Admin_Sidebar.vue'
import store from '../store/store'

const router = useRouter()
const { width } = useWindowSize()

// State
const teacher = ref(null)
const contents = ref([])
const isLoading = ref(true)
const error = ref(null)
const isDeleting = ref(false)
const isDeletingId = ref(null)

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar)

// Methods
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const navigateToUpdate = (id) => {
  router.push(`/admin_contents/update/${id}`)
}

const navigateToView = (id) => {
  router.push(`/admin_contents/${id}`)
}

const getUser = async () => {
  try {
    const response = await axios.get('/api/user', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    teacher.value = response.data
    teacher.value.image = new URL(teacher.value.image, import.meta.url)
    return teacher.value
  } catch (err) {
    console.error('Error fetching user:', err)
    error.value = 'Failed to load user data'
    if (!localStorage.getItem('token')) {
      router.push('/')
    }
    return null
  }
}

const getContents = async () => {
  try {
    isLoading.value = true
    const user = await getUser()
    if (!user) return

    const response = await axios.get(`/api/contents/${user.id}`)
    contents.value = response.data.map(content => ({
      ...content,
      thumb: new URL(content.thumb, import.meta.url)
    }))
  } catch (err) {
    console.error('Error fetching contents:', err)
    error.value = 'Failed to load contents'
  } finally {
    isLoading.value = false
  }
}

const deleteContent = async (id) => {
  const background = getComputedStyle(document.documentElement).getPropertyValue('--background')
  const text_dark = getComputedStyle(document.documentElement).getPropertyValue('--text_dark')
  const button4 = getComputedStyle(document.documentElement).getPropertyValue('--button4')

  try {
    const result = await Swal.fire({
      title: 'Are you sure?',
      text: 'Content will be deleted permanently!',
      icon: 'warning',
      color: text_dark,
      background: background,
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: button4,
      confirmButtonText: 'Yes, delete it!',
      showLoaderOnConfirm: true,
      preConfirm: async () => {
        try {
          isDeletingId.value = id
          isDeleting.value = true
          await axios.delete(`/api/contents/delete/${id}`)
          return true
        } catch (error) {
          Swal.showValidationMessage(`Delete failed: ${error.response?.data?.message || error.message}`)
          return false
        } finally {
          isDeletingId.value = null
          isDeleting.value = false
        }
      },
      allowOutsideClick: () => !Swal.isLoading()
    })

    if (result.isConfirmed) {
      await Swal.fire({
        title: 'Deleted!',
        text: 'Content has been deleted successfully.',
        icon: 'success',
        color: text_dark,
        background: background
      })
      await getContents()
    }
  } catch (err) {
    console.error('Error in delete operation:', err)
    Swal.fire({
      title: 'Error!',
      text: 'Failed to delete content. Please try again.',
      icon: 'error',
      color: text_dark,
      background: background
    })
  }
}

// Lifecycle
onMounted(async () => {
  if (!localStorage.getItem('token')) {
    router.push('/')
    return
  }
  await getContents()
})
</script>

<style scoped>
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.list-leave-active {
  position: absolute;
}
</style>