import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});


window.Echo.channel(`personal_chat`)
    .listen('MessageSent', (event) => {
        console.log('New Message Event:', event.message.message, event.message.file, event.time);

        const messageList = document.getElementById('messageList');

        if (event.message.message) {
            const messageText = document.createElement('p');
            messageText.innerText = event.message.message;
            messageList.append(messageText);
        }

        if (event.message.file) {
            const fileExtension = event.message.file.split('.').pop().toLowerCase();
            const fileContainer = document.createElement('div');
        
            const baseUrl = window.location.origin;
            const fileUrl = event.message.file.startsWith('http') ? event.message.file : `${baseUrl}/${event.message.file}`;
        
            if (['jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp'].includes(fileExtension)) {
                const imgPreview = document.createElement('img');
                imgPreview.src = fileUrl;
                imgPreview.alt = 'Image Preview';
                imgPreview.style.maxWidth = '200px';
                imgPreview.style.display = 'block';
                imgPreview.classList.add('mt-2', 'rounded');
                fileContainer.appendChild(imgPreview);
        
            } else if (['mp4', 'mov', 'avi', 'mkv'].includes(fileExtension)) {
                const videoPreview = document.createElement('video');
                videoPreview.src = fileUrl;
                videoPreview.controls = true;
                videoPreview.style.maxWidth = '200px';
                videoPreview.classList.add('mt-2');
                fileContainer.appendChild(videoPreview);
        
            } else {
                const fileLink = document.createElement('a');
                fileLink.href = fileUrl;
                fileLink.target = '_blank';
                fileLink.innerText = 'Download File';
                fileLink.classList.add('d-block', 'mt-2', 'text-decoration-none', 'text-primary');
                fileContainer.appendChild(fileLink);
            }
        
            messageList.append(fileContainer);
        }
        

        if (event.time) {
            const timestamp = document.createElement('p');
            timestamp.innerText = event.time;
            messageList.append(timestamp);
        }

    });

