<template>
    <div class="mx-auto mt-8">
        <div class="w-4/5 flex mx-auto justify-center space-x-2 items-start">
            <div class="max-w-xl min-w-64 w-4/5 bg-white rounded-xl border-2 border-gray-200 p-4">
                <div class="flex justify-between items-center">
                    <h2 class="font-light text-gray-500">Chats</h2>
                    <svg @click.prevent="toggleShowUsers" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-500 hover:cursor-pointer">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </div>

                <div v-for="chat in chats" class="font-bold my-2 pb-2 border-b ">
                    <Link :href="route('chats.show', chat.id)" class="flex items-center justify-between text-left w-full py-2">
                        <div class="flex items-center space-x-4">
                            <div  v-if="!chat.is_group" class="relative size-10 overflow-hidden bg-gray-200 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="absolute top-1 text-gray-400 size-10">
                                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div  v-if="chat.is_group" class="relative size-10 overflow-hidden bg-gray-200 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="absolute top-1 text-gray-400 size-10">
                                    <path d="M4.5 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM14.25 8.625a3.375 3.375 0 1 1 6.75 0 3.375 3.375 0 0 1-6.75 0ZM1.5 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM17.25 19.128l-.001.144a2.25 2.25 0 0 1-.233.96 10.088 10.088 0 0 0 5.06-1.01.75.75 0 0 0 .42-.643 4.875 4.875 0 0 0-6.957-4.611 8.586 8.586 0 0 1 1.71 5.157v.003Z" />
                                </svg>
                            </div>
                            <div>
                                <div class="flex space-x-1 items-center justify-start">
                                    <svg v-if="chat.is_group" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-3.5">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4H6Zm7.25-2.095c.478-.86.75-1.85.75-2.905a5.973 5.973 0 0 0-.75-2.906 4 4 0 1 1 0 5.811ZM15.466 20c.34-.588.535-1.271.535-2v-1a5.978 5.978 0 0 0-1.528-4H18a4 4 0 0 1 4 4v1a2 2 0 0 1-2 2h-4.535Z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="line-clamp-1">
                                        {{ chat.title ?? chat.chat_with.name }}
                                    </div>
                                </div>
                                <div v-if="chat.last_message" class="flex space-x-0.5 items-center">
                                    <svg v-if="chat.last_message.is_owner" :class="['size-4', chat.last_message.is_read ? 'text-green-500' : 'text-gray-400']" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 11.917 9.724 16.5 19 7.5"/>
                                    </svg>
                                    <p class="text-sm font-normal line-clamp-1">
                                        <span v-if="!chat.last_message.is_owner"><span class="text-blue-500">{{ chat.last_message.user.name }}</span>: </span>
                                        <span class="text-gray-600">{{ chat.last_message.text }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div v-if="chat.unread_messages_count > 0" class="bg-blue-500 text-white rounded-full py-1 px-2 text-xs text-center">{{ chat.unread_messages_count }}</div>
                    </Link>
                </div>
            </div>

            <div v-if="isShowUsers" class="min-w-48 max-w-sm w-2/5 bg-white rounded-xl border-2 border-gray-200 p-4">
                <div class="flex justify-between mb-2 items-center">
                    <h2 class="font-light text-gray-500">Users</h2>

                    <button v-if="!groupMode" @click.prevent="toggleGroupMode" class="text-sm text-white bg-blue-500 rounded-full px-3 py-1">new group</button>

                    <div v-if="groupMode" class="space-x-2">
                        <button @click.prevent="toggleGroupMode" class="text-sm">cancel</button>
                        <button @click.prevent="storeGroup" :disabled="userIds.length < 1" :class="['text-sm font-bold', userIds.length < 1 ? 'text-gray-300' : 'text-blue-500']">create</button>
                    </div>
                </div>

                <div v-if="groupMode" class="mb-2">
                    <input v-model='title' type="text" class="w-full text-sm border-2 border-gray-300 p-1 rounded-full px-2" placeholder="New group">
                </div>

                <div v-for="user in users" class="flex justify-between items-center font-bold py-1 rounded-lg px-4 hover:bg-gray-100">
                        <button @click.prevent="startChat(user.id)" class="flex items-center space-x-2 text-left py-2">
                            <div class="relative w-7 h-7 overflow-hidden bg-gray-200 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="absolute top-1 text-gray-400 size-7">
                                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>{{ user.name }}</div>
                        </button>
                    <input @click="toggleUser(user.id)" v-if="groupMode" type="checkbox" class="border-gray-300 border-2">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Link} from "@inertiajs/vue3";

export default {
    name: "Index",
    layout: AuthenticatedLayout,

    components: {
        Link
    },

    props: [
        'users', 'chats'
    ],

    data() {
        return {
            isShowUsers: false,
            groupMode: false,
            title: '',
            userIds: [],
        }
    },

    created() {
        window.Echo.private(`users_${this.$page.props.auth.user.id}`)
            .listen('.store_message_status', res => {
                if (this.$page.url !== '/chats') return
                if (this.chats.map(chat => { return chat.id }).includes(res.chat_id)) {
                    this.chats.filter(chat => {
                        if (chat.id === res.chat_id) {
                            chat.unread_messages_count++
                            chat.last_message = res.last_message
                        }
                    })
                } else {
                    this.getChat(res.chat_id)
                }
            })
            .listen('.store_group_chat', res => {
                if (this.$page.url !== '/chats') return
                this.getChat(res.chat_id)
            })
            .listen('.update_message_status', res => {
                if (this.$page.url !== '/chats') return
                this.chats.filter(chat => {
                    if (chat.id === res.chat_id) {
                        chat.last_message.is_read = true
                    }
                })
            })
    },

    methods: {
        startChat(id) {
            this.$inertia.post('/chats', { users: [id], title: null })
        },

        toggleShowUsers() {
            this.isShowUsers = !this.isShowUsers

            this.groupMode = false
            this.userIds = []
            this.title = ''
        },

        storeGroup() {
            let groupTitle = this.title === '' ? 'New group' : this.title
            this.$inertia.post('/chats', { users: this.userIds, title: groupTitle })
        },

        toggleGroupMode() {
            this.groupMode = !this.groupMode

            this.userIds = []
            this.title = ''
        },

        toggleUser(id) {
            let index = this.userIds.indexOf(id)
            if (index === -1) {
                this.userIds.push(id)
            } else {
                this.userIds.splice(index, 1)
            }
        },

        getChat(id) {
            axios.get(`/chats/${id}/get`)
                .then(res => {
                    console.log(res)
                    this.chats.push(res.data)
                })
        }
    }
}
</script>

<style scoped>

</style>
