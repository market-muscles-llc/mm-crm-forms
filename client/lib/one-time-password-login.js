import { reactive } from 'vue'
import { opnFetch } from "~/composables/useOpnApi.js"
import { fetchAllWorkspaces } from "~/stores/workspaces.js"

const form = useForm({
  code: "",
  external_user_id: "",
})

export const state = reactive({
  loading: true,
  pendingResolution: true,
})

export const resolveOneTimePassword = async (authStore, formsStore, workspaceStore) => {
  // Check if we are in an iFrame
  if (window.parent === window.self) {
    state.pendingResolution = false
    return
  }

  console.log("🟩 getUserData from app")
  const encodedUserData = await getUserDataFromFrame()

  form.code = encodedUserData['code']
  form.external_user_id = encodedUserData['user_id']

  form
    .post("one-time-password/login")
    .then(async (data) => {
      // Save the token.
      authStore.setToken(data.token)

      const [userDataResponse, workspacesResponse] = await Promise.all([
        opnFetch("user"),
        fetchAllWorkspaces(),
      ])
      authStore.setUser(userDataResponse)
      workspaceStore.set(workspacesResponse.data.value)

      // Load forms
      formsStore.loadAll(workspaceStore.currentId)

      const intendedUrlCookie = useCookie("intended_url")
      const router = useRouter()

      if (intendedUrlCookie.value) {
        router.push({ path: intendedUrlCookie.value })
        useCookie("intended_url").value = null
      } else {
        router.push({ name: "home" })
      }
    })
    .catch((error) => {
      console.log('🟥 OneTimePassword Error', error)
      state.pendingResolution = false
    })
}

const getUserDataFromFrame = async () => {
  return await new Promise((resolve) => {
    window.parent.postMessage(
      {
        message: "REQUEST_OPNFORM_OTP_DATA",
      },
      "*"
    )

    window.addEventListener("message", ({ data }) => {
      if (data.message === "REQUEST_OPNFORM_OTP_DATA_RESPONSE") {
        resolve(data.payload)
      }
    })
  })
}

export default { resolveOneTimePassword }
