<script setup>
import { computed, nextTick, ref } from 'vue'
import VBtn from '../components/VBtn.vue'

const props = defineProps({
  items: {
    type: Array,
    default: () => []
  }
})

/**
 * @type {{value: String}}
 */
const newEmail = ref('')

/**
 * @type {{value: HTMLDialogElement?}}
 */
const dialog = ref(null)

/**
 * @type {Object.<String, Number|String>?}
 */
const editingEmployee = ref(null)

/**
 * @type {Object.<String, Number|String>[]}
 * @returns {Number}
 */
const getAvgSalary = (employees) => {
  if (!employees.length)
    return 0

  return Math.round(employees.reduce((total, { salary }) => total + salary, 0) / employees.length)
}

const onEmailUpdate = () => {
  dialog.value.close()

  if (!newEmail.value)
    return

  const tmpEmail = editingEmployee.value.email
  editingEmployee.value.email = 'Updating...'

  const body = new FormData()
  body.append('id', editingEmployee.value.record_id)
  body.append('sessionId', editingEmployee.value.session_id)
  body.append('email', newEmail.value)

  // We will be using alerts just for the simplicity of the example
  fetch('/api/v1/service/update-email', { method: 'POST', body })
    .then((response) => {
      if (!response.ok) {
        alert('Not updated...') // eslint-disable-line no-alert
        editingEmployee.value.email = tmpEmail
      }
      return response.json()
    })
    .then((data) => {
      if (!data.status) {
        alert(data[0] || data.text) // eslint-disable-line no-alert
        editingEmployee.value.email = tmpEmail
      } else {
        editingEmployee.value.email = newEmail.value
      }
    })
    .catch(() => {
      alert('Network error') // eslint-disable-line no-alert
      editingEmployee.value.email = tmpEmail
    })
    .finally(() => {
      newEmail.value = ''
    })
}

/**
 * @param {Object.<String, Number|String>}
 */
const setToEditing = (employee) => {
  editingEmployee.value = employee

  nextTick(() => {
    dialog.value.showModal()
  })
}

const sessionId = computed(() => (props.items.length ? props.items[0].session_id : ''))
const companies = computed(() => {
  const obj = {}

  for (let i = 0; i < props.items.length; i++) {
    const item = props.items[i]

    if (!obj[item.company_name])
      obj[item.company_name] = { name: item.company_name, employees: [item] }
    else
      obj[item.company_name].employees.push(item)
  }

  return Object.values(obj)
})
</script>

<template>
  <div class="text-gray-700 pb-10">Your session id is: <span>{{sessionId}}</span></div>
  <table v-for="{ name, employees } in companies" :key="name" class="table-fixed border border-gray-300 my-3 rounded-xl">
    <thead>
      <tr>
        <td colspan="2" class="font-bold text-left pl-2 pt-4 pb-6">{{employees[0].company_name}}</td>
        <td class="text-gray-700 text-right pr-2">Avg. salary: ${{getAvgSalary(employees)}}</td>
      </tr>
      <tr class="border-b border-gray-300 text-sm text-gray-500">
        <th class="p-2 text-left font-normal">Employee name</th>
        <th class="p-2 text-left font-normal">Email address</th>
        <th class="p-2 text-right font-normal">Salary</th>
      </tr>
    </thead>
    <tbody class="bg-gray-100">
      <tr v-for="e in employees" :key="e.record_id" class="border-b border-gray-200 table-row">
        <td class="p-2 text-left">{{e.name}}</td>
        <td class="p-2 text-left hover:underline" title="Edit email" @click.stop.prevent="setToEditing(e)">{{e.email}}</td>
        <td class="p-2 text-right">${{e.salary}}</td>
      </tr>
    </tbody>
  </table>
  <dialog ref="dialog" class="p-10">
    <div v-if="editingEmployee" class="flex flex-col gap-4">
      <p>Updating the email for {{editingEmployee.name}}</p>
      <input type="text" v-model="newEmail" placeholder="Enter new email..." class="p-2 border-1 border-gray-200 rounded-lg" autofocus />
      <VBtn label="Update" @click.stop="onEmailUpdate" />
    </div>
  </dialog>
</template>
