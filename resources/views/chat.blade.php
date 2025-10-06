@extends('layouts.app')

@section('content')

<div class="jey-meet-index" style="display:flex;min-height:100vh;">
    <!-- ✅ Sidebar -->
    <div class="sidebar">
        <!-- ✅ Professional Title Bar -->
        <div class="title-bar">
            <span class="title-text">V-Meet</span>
        </div>

        <!-- ✅ User ID -->
        <div class="user-id">
            ID: {{ Auth::user()->user_id ?? '-----' }}
        </div>

        <!-- ✅ Navigation -->
        <nav>
            <a href="/chat" class="nav-link">
                <i data-lucide="message-square"></i>
                <span>Chat</span>
            </a>
            <a href="/meeting" class="nav-link">
                <i data-lucide="video"></i>
                <span>Meetings</span>
            </a>
            <a href="/schedule" class="nav-link">
                <i data-lucide="calendar"></i>
                <span>Schedule</span>
            </a>
            <a href="/fileshare" class="nav-link">
                <i data-lucide="folder"></i>
                <span>Files</span>
            </a>

            <!-- ✅ Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i data-lucide="log-out"></i> <span>Logout</span>
                </button>
            </form>

            <!-- ✅ Contacts -->
            @if(!empty($sidebarContacts))
            <div class="contacts-label">Contacts</div>
            <ul class="contacts-list">
                @foreach($sidebarContacts as $c)
                    <li>
                        <a href="/chat?user={{ $c->id }}" class="contact-link">
                            <i data-lucide="user"></i> {{ $c->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
            @endif
        </nav>
    </div>

<div class="chat-container">
  <div class="chat-header">💬 Swivel Chatbox</div>
  <div class="chat-box" id="chatBox"></div>

  <!-- Input Section -->
  <div class="chat-input">
    <button id="attachBtn"><i class="fas fa-paperclip"></i></button>
    <button id="cameraBtn"><i class="fas fa-camera"></i></button>
    <button id="emojiBtn"><i class="far fa-smile"></i></button>
    <input type="text" id="messageInput" placeholder="Type a message...">
    <button id="sendBtn"><i class="fas fa-paper-plane"></i></button>
    <button id="micBtn"><i class="fas fa-microphone"></i></button>
  </div>

  <!-- Attachment Menu -->
  <div class="attachment-menu" id="attachmentMenu">
    <label><input type="file" accept="image/*" hidden onchange="sendFile(event)"> 📷 Photo</label>
    <label><input type="file" accept="video/*" hidden onchange="sendFile(event)"> 🎥 Video</label>
    <label><input type="file" accept=".pdf,.doc,.docx" hidden onchange="sendFile(event)"> 📄 Document</label>
  </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    font-family: Arial, sans-serif;
    background: #f3f4f6;
  }
  .chat-container {
    display: flex;
    flex-direction: column;
    height: 100vh;  /* ✅ Full screen */
    width: 100vw;   /* ✅ Full width */
    background: #fff;
    box-shadow: 0 0 12px rgba(0,0,0,0.15);
    overflow: hidden;
  }
  .chat-header {
    background: #2196f3;
    color: white;
    text-align: center;
    padding: 15px;
    font-size: 22px;
    font-weight: bold;
    text-shadow: 1px 2px 3px black;
  }
  .chat-box {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #ece5dd;
    display: flex;
    flex-direction: column;
  }
  .message {
    margin: 8px 0;
    max-width: 65%;
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 14px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    line-height: 1.4;
  }
  .left { background: #fff; align-self: flex-start; }
  .right { align-self: flex-end; color: #000; }
  .right.color1 { background: #dcf8c6; }
  .right.color2 { background: #a7ffeb; }
  .right.color3 { background: #ffe082; }
  .right.color4 { background: #ffccbc; }
  .right.color5 { background: #d1c4e9; }
  .chat-input {
    display: flex;
    align-items: center;
    padding: 10px;
    background: #f0f0f0;
    border-top: 1px solid #ddd;
  }
  .chat-input button {
    background: none;
    border: none;
    font-size: 20px;
    margin: 0 5px;
    cursor: pointer;
    color: #555;
    transition: color 0.2s;
  }
  .chat-input button:hover { color: #2196f3; }
  .chat-input input[type="text"] {
    flex: 1;
    padding: 10px 14px;
    border-radius: 20px;
    border: 1px solid #ccc;
    outline: none;
    font-size: 14px;
    margin: 0 6px;
  }
  .attachment-menu {
    display: none;
    position: absolute;
    bottom: 80px;
    left: 20px;
    background: white;
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    z-index: 10;
  }
  .attachment-menu label {
    display: block;
    margin: 5px 0;
    font-size: 14px;
    cursor: pointer;
  }
  .sidebar {
        background: #1f2233;
        padding: 1.5rem;
        width: 250px;
        min-height: 100vh;
    }

    /* Title bar */
    .title-bar {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        padding: 0.8rem 1rem;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.25);
    }
    .title-text {
        font-size: 1.3rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: 0.5px;
    }

    .user-id {
        margin-bottom: 1.5rem;
        font-size: 1.05rem;
        color: #f59e42;
        font-weight: 600;
        text-align: center;
    }

    /* Nav links */
    .nav-link {
        color: #fff;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 1rem;
        padding: 0.6rem 0.8rem;
        border-radius: 8px;
        transition: background 0.2s;
    }
    .nav-link:hover {
        background: #2e324a;
    }

    /* Logout button */
    .logout-btn {
        background: #ef4444;
        color: #fff;
        border: none;
        width: 100%;
        padding: 0.6rem;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        font-size: 1rem;
        margin-top: 1rem;
    }
    .logout-btn:hover {
        background: #dc2626;
    }

    /* Contacts */
    .contacts-label {
        margin-top: 1.5rem;
        color: #cbd5e1;
        font-weight: 600;
    }
    .contacts-list {
        list-style: none;
        padding: 0;
        margin-top: 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }
    .contact-link {
        color: #fff;
        text-decoration: none;
        background: #3a3f5a;
        padding: 0.45rem 0.7rem;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .contact-link:hover {
        background: #4b526e;
    }

    /* Cards */
    .card {
        flex: 1;
        background: #e0e7ef;
        padding: 2rem;
        border-radius: 12px;
        text-align: center;
        text-decoration: none;
        box-shadow: 0 2px 8px #d1d9e6;
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-3px);
    }
    .card-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-top: 1rem;
    }
    .card-sub {
        color: #6b7280;
        margin-top: 0.5rem;
    }

    /* Buttons */
    .btn-blue, .btn-green, .btn-orange {
        padding: 1rem 2rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        color: #fff;
    }
    .btn-blue { background: #3b82f6; }
    .btn-blue:hover { background: #2563eb; }
    .btn-green { background: #10b981; }
    .btn-green:hover { background: #059669; }
    .btn-orange { background: #f59e42; }
    .btn-orange:hover { background: #d97706; }

    /* Tips box */
    .tips-box {
        background: #fff;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button/dist/index.umd.min.js"></script>
<script>
  const chatBox = document.getElementById("chatBox");
  const messageInput = document.getElementById("messageInput");
  const attachmentMenu = document.getElementById("attachmentMenu");
  const micBtn = document.getElementById("micBtn");
  const sendBtn = document.getElementById("sendBtn");

  const colors = ["color1", "color2", "color3", "color4", "color5"];
  let colorIndex = 0;

  // Add message
  function addMessage(content) {
    const div = document.createElement("div");
    div.className = "message right " + colors[colorIndex];
    div.textContent = content;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
    colorIndex = (colorIndex + 1) % colors.length;
  }

  // Send text
  function sendMessage() {
    const msg = messageInput.value.trim();
    if (!msg) return;

    addMessage(msg);
    messageInput.value = "";

    fetch("/messages", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
      },
      body: JSON.stringify({ content: msg })
    })
    .then(res => res.json())
    .then(data => console.log("Saved:", data))
    .catch(err => console.error("Error:", err));
  }

  function sendFile(event) {
    const file = event.target.files[0];
    if (!file) return;
    addMessage("📎 Sent: " + file.name);
    attachmentMenu.style.display = "none";
  }

  // Events
  sendBtn.onclick = sendMessage;
  messageInput.addEventListener("keydown", e => {
    if (e.key === "Enter") {
      e.preventDefault();
      sendMessage();
    }
  });

  document.getElementById("attachBtn").onclick = () => {
    attachmentMenu.style.display = attachmentMenu.style.display === "block" ? "none" : "block";
  };

  document.getElementById("cameraBtn").onclick = () => {
    const input = document.createElement("input");
    input.type = "file";
    input.accept = "image/*";
    input.capture = "environment";
    input.onchange = sendFile;
    input.click();
  };

  // Mic recording
  let mediaRecorder, chunks = [];
  micBtn.onclick = async () => {
    if (!mediaRecorder || mediaRecorder.state === "inactive") {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      mediaRecorder = new MediaRecorder(stream);
      chunks = [];
      mediaRecorder.ondataavailable = e => chunks.push(e.data);
      mediaRecorder.onstop = () => {
        const fileName = "voice_" + Date.now() + ".ogg";
        addMessage("🎤 " + fileName);
      };
      mediaRecorder.start();
      micBtn.innerHTML = '<i class="fas fa-stop"></i>';
    } else {
      mediaRecorder.stop();
      micBtn.innerHTML = '<i class="fas fa-microphone"></i>';
    }
  };

  // Emoji
  const picker = new window.EmojiButton();
  const emojiBtn = document.getElementById("emojiBtn");
  picker.on("emoji", emoji => {
    messageInput.value += emoji;
    messageInput.focus();
  });
  emojiBtn.addEventListener("click", () => picker.togglePicker(emojiBtn));
</script>
@endpush

