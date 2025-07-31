import { ref, reactive } from 'vue'

const toasts = ref([])

export function useToast() {
  const addToast = (toast) => {
    const id = Date.now() + Math.random()
    const newToast = {
      id,
      type: 'info',
      duration: 5000,
      ...toast
    }
    
    toasts.value.push(newToast)
    
    return id
  }

  const removeToast = (id) => {
    const index = toasts.value.findIndex(toast => toast.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }

  const success = (title, message = '', duration = 5000) => {
    return addToast({ type: 'success', title, message, duration })
  }

  const error = (title, message = '', duration = 7000) => {
    return addToast({ type: 'error', title, message, duration })
  }

  const warning = (title, message = '', duration = 6000) => {
    return addToast({ type: 'warning', title, message, duration })
  }

  const info = (title, message = '', duration = 5000) => {
    return addToast({ type: 'info', title, message, duration })
  }

  const clear = () => {
    toasts.value = []
  }

  return {
    toasts,
    addToast,
    removeToast,
    success,
    error,
    warning,
    info,
    clear
  }
}
