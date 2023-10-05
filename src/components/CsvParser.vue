<script setup>
import { computed, onMounted, ref } from 'vue'
import { fasTriangleExclamation, fasCloudArrowUp, fasX } from '../assets/icons/fontawesome6-icons'
import VIcon from './VIcon.vue'
import VBtn from './VBtn.vue'
import EmployeesTable from '../blocks/EmployeesTable.vue'

/**
 * @type {{value: HTMLFormElement?}}
 */
const form = ref(null)

/**
 * @type {{value: HTMLElement?}}
 */
const progressDiv = ref(null)

/**
 * @type {{value: File?}}
 */
const csv = ref(null)

/**
 * @type {{value: String}}
 */
const sessionId = ref('')

/**
 * @type {{value: HTMLLabelElement?}}
 */
const drop = ref(null)

/**
 * @type {{value: HTMLInputElement?}}
 */
const input = ref(null)

/**
 * @type {{value: Boolean}}
 */
const isDraggable = ref(false)

/**
 * @type {{value: String}}
 */
const errorMessage = ref('')

/**
 * @type {{value: Object.<String, String|Number>[]}}
 */
const rows = ref([])

/**
 * @type {{value: Boolean}}
 */
const isDragging = ref(false)

const props = defineProps({
  type: {
    type: String,
    default: 'button'
  },
  accept: {
    type: String
  },
  icon: {
    type: String,
    default: ''
  },
  iconClass: {
    type: String,
    default: 'w-6 h-6'
  }
})

onMounted(() => {
  isDraggable.value = !!drop.value && 'ondragstart' in drop.value && 'ondrop' in drop.value

  if (!isDraggable.value)
    errorMessage.value = 'Cannot use drag and drop feature...'
})

/**
 * @param {Number} bytes
 */
const convertSize = (bytes) => {
  const size = bytes / 1024

  if (size < 1000)
    return size.toFixed(2) + 'KB'

  return (size / 1024).toFixed(2) + 'MB'
}

const resetUploader = () => {
  csv.value = null
  form.value.reset()
  rows.value = []
  progressDiv.value.style = null
  sessionId.value = ''
}

/**
 * @type {ProgressEvent}
 */
const updateProgress = (evt) => {
  let percent = parseInt((evt.loaded * 100) / evt.total)

  if (percent < 5)
    percent = 5
  else if (percent > 100)
    percent = 100

  progressDiv.value.style.width = percent + '%'
}

const uploadFile = () => {
  errorMessage.value = ''

  const formData = new FormData()
  formData.append('file', csv.value)

  const xhr = new XMLHttpRequest()
  xhr.upload.addEventListener('progress', updateProgress, false)

  xhr.onreadystatechange = () => {
    if (xhr.readyState === 4 && xhr.status === 200) {
      try {
        const response = JSON.parse(xhr.responseText)

        if (response.status) {
          rows.value = response.data
        } else {
          errorMessage.value = response.text
        }
      } catch (error) {
        errorMessage.value = 'API did not return proper response...'
        resetUploader()
      }
    }
  }

  xhr.open(form.value.method, form.value.action, true)
  xhr.send(formData)
}

/**
 * @type {MouseEvent}
 */
const onLabelClick = (evt) => {
  if (csv.value)
    evt.preventDefault()
}

/**
 * @param {DragEvent} evt
 */
const onDragover = (evt) => {
  evt.preventDefault()

  if (csv.value || !isDraggable.value)
    return

  isDragging.value = true
}

/**
 * @param {DropEvent} evt
 */
const onDrop = (evt) => {
  evt.preventDefault()
  isDragging.value = false

  if (csv.value)
    return

  const [file] = evt.dataTransfer.files

  if (!file || file.type !== props.accept)
    return

  csv.value = file

  const fileList = new DataTransfer()
  fileList.items.add(file)
  input.value.files = fileList.files

  uploadFile()
}

/**
 * @param {DragEvent} evt
 */
const onDragLeave = (evt) => {
  evt.preventDefault()
  isDragging.value = false
}

const handleUploadedFile = () => {
  const [file] = input.value.files

  if (!file)
    return

  csv.value = file
  uploadFile()
}

/**
 * @type {SubmitEvent}
 */
const onFormSubmit = (evt) => {
  evt.preventDefault()
  uploadFile()
}

const onSessionId = () => {
  if (!sessionId.value)
    return

  fetch('/api/v1/service/get-by-session-id?id=' + sessionId.value)
    .then(response => response.json())
    .then((response) => {
      if (response.status) {
        rows.value = response.data
      } else {
        alert('No session found...') // eslint-disable-line no-alert
      }
    })
}

const dropClasses = computed(() => 'block bg-white rounded-xl border-2 w-full border-gray-300 hover:border-blue-400 px-20 py-24 transition-colors duration-300 cursor-pointer' + (isDragging.value ? ' border-blue-400' : '')) // eslint-disable-line max-len
const fileSize = computed(() => ((csv.value) ? convertSize(csv.value.size) : ''))
</script>

<template>
  <form
    ref="form"
    class="w-full max-w-3xl mx-auto"
    method="post"
    action="/api/v1/service/parse-csv"
    enctype="multipart/form-data"
    novalidate
    @submit="onFormSubmit"
  >
    <div v-if="errorMessage" class="flex flex-row items-center justify-center gap-2 text-red-500">
      <VIcon :name="fasTriangleExclamation" class="w-4 h-4" />
      <span>{{errorMessage}}</span>
    </div>
    <input
      type="file"
      ref="input"
      id="file_uploader"
      name="file_uploader"
      class="hidden"
      :accept="accept"
      @change="handleUploadedFile"
    />
    <label
      for="file_uploader"
      ref="drop"
      :class="dropClasses"
      @dragover="onDragover"
      @dragenter="onDragover"
      @dragleave="onDragLeave"
      @drop="onDrop"
      @click="onLabelClick"
    >
      <div class="flex flex-col gap-2 text-center">
        <div v-if="rows.length" class="flex flex-row justify-center pb-10">
          <VBtn label="Reset" :icon="fasX" iconClass="w-4 h-4" @click.stop.prevent="resetUploader" />
        </div>
        <VIcon v-else :name="fasCloudArrowUp" class="text-gray-200 h-28 w-28 mx-auto" />
        <span class="text-2xl text-gray-600">
          <template v-if="!csv">Upload file</template>
          <template v-else>{{csv.name}} ({{fileSize}})</template>
        </span>
        <div ref="progressDiv" class="h-1 transition-[width] w-0 bg-blue-500 mx-auto rounded-full" />
        <EmployeesTable v-if="rows.length" :items="rows" />
        <template v-else>
          <span class="text-gray-400">
            <template v-if="isDraggable">You can either click here or drag and drop a new file</template>
            <template v-else>Click here to upload a file</template>
          </span>
        </template>
      </div>
    </label>
  </form>
  <div v-if="!rows.length" class="flex flex-row justify-center py-4">
    <input type="text" v-model="sessionId" placeholder="Or enter session id..." class="p-2 rounded-lg" @keydown.enter="onSessionId" />
  </div>
</template>
