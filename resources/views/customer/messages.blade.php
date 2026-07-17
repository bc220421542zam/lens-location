<x-layouts.customer>
<div x-data="messagesPage()" x-init="init()" class="flex gap-4" style="height: 80vh;">

    {{-- CONVERSATION LIST --}}
    <div class="w-full sm:w-80 shrink-0 card chart-transition rounded-2xl border-l-3 border-indigo-400 chart-transition rounded-2xl border-l-3 border-indigo-400 p-0 overflow-hidden flex flex-col {{ $conversation ? 'hidden sm:flex' : 'flex' }}">
        <div class="px-5 py-4 border-b border-indigo-100">
            <h2 class="text-lg font-semibold text-indigo-900">Messages</h2>
            <p class="text-xs text-gray-500">Your conversations with owners</p>
        </div>

        <div class="flex-1 overflow-y-auto">
            @forelse($conversations as $conv)
                @php
                    $other = $conv->owner;
                    $last = $conv->lastMessage;
                    $active = $conversation && $conversation->id === $conv->id;
                @endphp
                <a href="{{ route('customer.messages', $conv->id) }}"
                   class="flex items-center gap-3 p-4 border-b border-indigo-50 hover:bg-indigo-50 transition {{ $active ? 'bg-indigo-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-900 font-medium text-sm shrink-0">
                        {{ strtoupper(substr($other->first_name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-indigo-900 truncate">
                            {{ $other->first_name ?? 'Owner' }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ $last?->attachment_path && !$last?->body ? '📎 Attachment' : ($last?->body ?? 'No messages yet') }}
                        </p>
                    </div>
                    @if($last && !$last->read_at && $last->sender_id !== auth()->id())
                        <span class="w-2 h-2 bg-indigo-600 rounded-full shrink-0"></span>
                    @endif
                </a>
            @empty
                <div class="text-center py-10 text-gray-400 px-4 rounded-2xl border-l-3 border-indigo-400 ">
                    <i class="fa-solid fa-message text-2xl mb-2"></i>
                    <p class="font-medium text-sm">No conversations yet</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- CHAT PANEL --}}
    <div class="flex-1 card chart-transition rounded-2xl border-l-3 border-indigo-400 p-0 overflow-hidden flex flex-col {{ $conversation ? 'flex' : 'hidden sm:flex' }}">
        @if($conversation)
            {{-- HEADER --}}
            <div class="flex items-center gap-3 px-5 py-4 border-b border-indigo-100 bg-white shrink-0">
                <a href="{{ route('customer.messages') }}" class="sm:hidden text-indigo-700">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-800 flex items-center justify-center text-white font-semibold text-sm shrink-0">
                    {{ strtoupper(substr($conversation->owner->first_name ?? 'O', 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-sm font-semibold text-indigo-900 leading-tight">
                        {{ $conversation->owner->first_name ?? 'Owner' }}
                    </h1>
                    <p class="text-xs text-gray-400">{{ $conversation->location->title ?? 'Conversation' }}</p>
                </div>
            </div>

            {{-- MESSAGE LIST --}}
            <div class="flex-1 overflow-y-auto px-5 py-4 bg-gray-50" id="message-list">
                @php $prevSender = null; @endphp

                @foreach($conversation->messages as $message)
                    @php
                        $isMine = $message->sender_id === auth()->id();
                        $isNewGroup = $message->sender_id !== $prevSender;
                        $prevSender = $message->sender_id;
                    @endphp

                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }} {{ $isNewGroup ? 'mt-5' : 'mt-1' }}">
                        <div class="flex {{ $isMine ? 'flex-row-reverse' : 'flex-row' }} items-start gap-2 max-w-[75%]">
                            <div class="w-8 h-8 shrink-0 {{ $isNewGroup ? '' : 'invisible' }}">
                                <div class="w-8 h-8 rounded-full {{ $isMine ? 'bg-indigo-800' : 'bg-indigo-200' }} flex items-center justify-center text-[11px] font-semibold {{ $isMine ? 'text-white' : 'text-indigo-800' }}">
                                    {{ strtoupper(substr($message->sender->first_name ?? 'U', 0, 2)) }}
                                </div>
                            </div>

                            <div class="flex flex-col {{ $isMine ? 'items-end' : 'items-start' }}">
                                @if($isNewGroup)
                                    <div class="flex items-center gap-2 mb-1 {{ $isMine ? 'flex-row-reverse' : '' }}">
                                        <span class="text-xs font-semibold text-indigo-900">
                                            {{ $isMine ? 'You' : ($message->sender->first_name ?? 'User') }}
                                        </span>
                                        <span class="text-[11px] text-gray-400">
                                            {{ $message->created_at->format('g:i A') }}
                                        </span>
                                    </div>
                                @endif

                                {{-- ATTACHMENT --}}
                                @if($message->attachment_path)
                                    <div class="mb-1">
                                        @if($message->attachment_type === 'image')
                                            <a href="{{ asset('storage/'.$message->attachment_path) }}" target="_blank">
                                                <img src="{{ asset('storage/'.$message->attachment_path) }}"
                                                     class="max-w-[220px] max-h-[220px] rounded-xl border border-indigo-100 object-cover">
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/'.$message->attachment_path) }}" target="_blank"
                                               class="flex items-center gap-2 px-3 py-2 rounded-xl border border-indigo-100 bg-white text-indigo-900 text-xs hover:bg-indigo-50 transition">
                                                <i class="fa-solid fa-file-lines text-indigo-500"></i>
                                                <span class="truncate max-w-[150px]">{{ $message->attachment_name }}</span>
                                                <i class="fa-solid fa-arrow-down text-indigo-400"></i>
                                            </a>
                                        @endif
                                    </div>
                                @endif

                                @if($message->body)
                                    <div class="px-4 py-2 text-sm rounded-2xl shadow-sm
                                        {{ $isMine
                                            ? 'bg-indigo-700 text-white'
                                            : 'bg-white text-indigo-900 border border-indigo-100' }}">
                                        {{ $message->body }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ATTACHMENT PREVIEW --}}
            <div x-show="attachmentName" x-cloak class="px-4 pt-3 border-t border-indigo-100 bg-white">
                <div class="flex items-center gap-2 bg-indigo-50 rounded-lg px-3 py-2 text-xs text-indigo-900">
                    <template x-if="attachmentPreviewUrl">
                        <img :src="attachmentPreviewUrl" class="w-8 h-8 rounded object-cover">
                    </template>
                    <template x-if="!attachmentPreviewUrl">
                        <i class="fa-solid fa-file-lines text-indigo-500"></i>
                    </template>
                    <span class="truncate flex-1" x-text="attachmentName"></span>
                    <button type="button" @click="removeAttachment()" class="text-gray-400 hover:text-red-500">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            {{-- COMPOSER --}}
            <form method="POST" action="{{ route('customer.messages.store', $conversation->id) }}"
                  enctype="multipart/form-data"
                  class="flex items-center gap-2 px-4 py-3 border-t border-indigo-100 bg-white shrink-0">
                @csrf

                <input type="file" x-ref="fileInput" name="attachment" class="hidden" @change="handleFile($event)">

                <button type="button" @click="$refs.fileInput.click()"
                    class="w-9 h-9 flex items-center justify-center rounded-full shrink-0 text-indigo-400 hover:bg-indigo-50 transition"
                    title="Attach image or file">
                    <i class="fa-solid fa-paperclip text-sm"></i>
                </button>

                <button type="button"
                    @click="toggleListening()"
                    class="w-9 h-9 flex items-center justify-center rounded-full shrink-0 transition"
                    :class="isListening ? 'bg-red-500 text-white animate-pulse' : 'text-indigo-400 hover:bg-indigo-50'"
                    title="Voice input">
                    <i class="fa-solid fa-microphone text-sm"></i>
                </button>

                <input type="text"
                    name="body"
                    x-model="messageText"
                    placeholder="Type a message..."
                    class="flex-1 border border-gray-200 rounded-full px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400">

                <button type="submit"
                    class="w-10 h-10 flex items-center justify-center rounded-full bg-indigo-700 text-white hover:bg-indigo-800 transition shrink-0">
                    <i class="fa-solid fa-paper-plane text-sm"></i>
                </button>
            </form>

            <p x-show="speechError" x-text="speechError" class="text-xs text-red-500 px-4 pb-2"></p>
        @else
            <div class="flex-1 flex flex-col items-center justify-center text-gray-400">
                <i class="fa-solid fa-comments text-4xl mb-3"></i>
                <p class="font-medium">Select a conversation to start chatting</p>
            </div>
        @endif
    </div>

</div>

<script>
function messagesPage() {
    return {
        messageText: '',
        isListening: false,
        speechError: '',
        recognition: null,
        attachmentName: '',
        attachmentPreviewUrl: null,

        init() {
            const list = document.getElementById('message-list');
            if (list) list.scrollTop = list.scrollHeight;

            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechRecognition) {
                this.speechError = 'Voice input is not supported in this browser.';
                return;
            }

            this.recognition = new SpeechRecognition();
            this.recognition.continuous = false;
            this.recognition.interimResults = true;
            this.recognition.lang = 'en-US';

            this.recognition.onresult = (event) => {
                let transcript = '';
                for (let i = 0; i < event.results.length; i++) {
                    transcript += event.results[i][0].transcript;
                }
                this.messageText = transcript;
            };

            this.recognition.onerror = (event) => {
                this.speechError = 'Voice input error: ' + event.error;
                this.isListening = false;
            };

            this.recognition.onend = () => {
                this.isListening = false;
            };
        },

        toggleListening() {
            if (!this.recognition) return;

            if (this.isListening) {
                this.recognition.stop();
                this.isListening = false;
            } else {
                this.speechError = '';
                this.messageText = '';
                this.recognition.start();
                this.isListening = true;
            }
        },

        handleFile(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.attachmentName = file.name;

            if (file.type.startsWith('image/')) {
                this.attachmentPreviewUrl = URL.createObjectURL(file);
            } else {
                this.attachmentPreviewUrl = null;
            }
        },

        removeAttachment() {
            this.$refs.fileInput.value = '';
            this.attachmentName = '';
            this.attachmentPreviewUrl = null;
        }
    }
}
</script>
</x-layouts.customer>