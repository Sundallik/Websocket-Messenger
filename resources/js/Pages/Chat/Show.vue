<template>
    <div class="w-4/5 pb-8 mx-auto flex justify-center items-start space-x-2">
        <div class="max-w-xl min-w-64 w-4/5 mt-8 rounded-xl overflow-hidden border-2 border-gray-200">

            <div :class="['flex items-center justify-between font-bold bg-white px-4 text-center', chat.is_group ? 'py-2' : 'py-4']">
                <Link :href="route('chats.index')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </Link>
                <div class="flex-1">
                    <div class="mb-0">{{ chat.title ?? chat.chat_with.name }}</div>
                    <div v-if="chat.is_group" class="text-sm text-gray-500 font-light">{{ users.length }} members</div>
                </div>
                <svg v-if="chat.is_group" @click.prevent="toggleUsers" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 hover:cursor-pointer">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </div>

            <div class="bg-gray-50 p-4">
                <div v-if="messages.length === 0" class="text-center text-gray-300 my-8">Let`s chat!</div>
                <div v-if="hasNext" class="text-center">
                    <button @click.prevent="loadNext" class="text-sm text-white bg-blue-500 rounded-full px-3 py-1 mb-4">load more</button>
                </div>
                <div v-for="message in messages.slice().reverse()" :class="['mb-2', message.is_owner ? 'text-end' : 'text-start']">
                    <div :class="['inline-block rounded-xl py-2 px-4 min-w-32 max-w-96', message.is_owner ? 'bg-green-200' : 'bg-blue-100']">
                        <div v-if="!message.is_owner" class="text-sm font-bold">{{ message.user.name }}</div>
                        <p :class="[message.is_owner ? 'text-start' : 'text-end']">{{ message.text }}</p>
                        <div :class="['flex text-xs text-gray-500 items-center space-x-1 justify-end']">
                            <div>{{ message.time }}</div>
                            <svg v-if="message.is_owner" :class="['size-4', message.is_read ? 'text-green-500' : 'text-gray-400']" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 11.917 9.724 16.5 19 7.5"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="relative w-full">
                    <input v-model="text" type="search" id="search-dropdown" class="block p-2.5 pl-4 w-full z-20 text-sm text-gray-900 bg-white rounded-full border-2 border-gray-300" placeholder="Message..." required />
                    <button @click.prevent="storeMessage" type="submit" class="absolute top-0 end-0 p-2.5 h-full text-sm font-medium text-white bg-blue-500 rounded-r-full hover:bg-blue-600">
                        <svg class="size-5 text-white rotate-90" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12 2a1 1 0 0 1 .932.638l7 18a1 1 0 0 1-1.326 1.281L13 19.517V13a1 1 0 1 0-2 0v6.517l-5.606 2.402a1 1 0 0 1-1.326-1.281l7-18A1 1 0 0 1 12 2Z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div v-if="isShowUsers" class="min-w-48 max-w-sm w-2/5 mt-8 rounded-xl overflow-hidden border-2 border-gray-200 bg-white p-4">
            <h2 class="font-light text-gray-500 mb-4">Members ({{ users.length }})</h2>
            <div v-for="user in users" class="flex justify-between items-center font-bold py-1 rounded-lg px-4 hover:bg-gray-100">
                <button @click.prevent="startChat(user.id)" class="flex items-center space-x-2 text-left py-2">
                    <div class="relative w-7 h-7 overflow-hidden bg-gray-200 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="absolute top-1 text-gray-400 size-7">
                            <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>{{ user.name }}</div>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import {Link} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    name: "Index",
    layout: AuthenticatedLayout,

    components: {
        Link
    },

    props: [
        'chat', 'messages', 'users', 'hasNext'
    ],

    data() {
        return {
            text: '',
            isShowUsers: false,
            page: 1,
        }
    },

    created() {
        window.Echo.channel(`chat_${this.chat.id}`)
            .listen('.store_message', res => {
                if (this.$page.url === `/chats/${res.message.chat_id}`) {
                    this.messages.unshift(res.message)
                    axios.patch('/messages_statuses', { message_id: res.message.id })
                }
            })

        window.Echo.private(`users_${this.$page.props.auth.user.id}`)
            .listen('.update_message_status', res => {
                if (this.$page.url === `/chats/${res.chat_id}`) {
                    this.messages.filter(message => {
                        if (message.is_read === false && message.user.id === res.user_id) {
                            message.is_read = true
                        }
                    })
                }
            })
    },

    methods: {
        storeMessage() {
            axios.post('/messages', { chat_id: this.chat.id, text: this.text })
                .then(res => {
                    this.messages.unshift(res.data)
                    this.text = ''
                })
        },

        loadNext() {
            axios.get(`/chats/${this.chat.id}?page=${++this.page}`)
                .then(res => {
                    this.$page.props.hasNext = res.data.has_next
                    this.messages.push(...res.data.messages)
                })
        },

        toggleUsers() {
            this.isShowUsers = !this.isShowUsers
        },

        startChat(id) {
            if (id !== this.$page.props.auth.user.id) {
                this.$inertia.post('/chats', { users: [id], title: null }, {
                    onSuccess: res => {
                        this.isShowUsers = false
                    }
                })
            }
        },
    }
}
</script>

<style scoped>

</style>
