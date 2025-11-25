// Utility: SHA-256 hashing
async function sha256(message) {
    const msgBuffer = new TextEncoder().encode(message);
    const hashBuffer = await crypto.subtle.digest("SHA-256", msgBuffer);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
}

// Canvas fingerprint
function getCanvasFingerprint() {
    let canvas = document.createElement('canvas');
    let ctx = canvas.getContext('2d');

    ctx.textBaseline = 'top';
    ctx.font = '16px Arial';
    ctx.fillText("Ezra Device Fingerprint", 2, 2);

    return canvas.toDataURL();
}

// Audio fingerprint
async function getAudioFingerprint() {
    let ctx = new AudioContext();
    let oscillator = ctx.createOscillator();
    let analyser = ctx.createAnalyser();
    oscillator.connect(analyser);
    analyser.connect(ctx.destination);
    oscillator.start(0);

    let arr = new Float32Array(analyser.frequencyBinCount);
    analyser.getFloatFrequencyData(arr);

    oscillator.stop();
    return arr.slice(0, 10).join(',');
}

// Generate the full fingerprint
async function generateFingerprint() {
    const data = {
        userAgent: navigator.userAgent,
        platform: navigator.platform,
        language: navigator.language,
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        screen: `${screen.width}x${screen.height}`,
        canvas: getCanvasFingerprint(),
        audio: await getAudioFingerprint()
    };

    const json = JSON.stringify(data);
    const fingerprint_id = await sha256(json);

    // You can also generate sub-hashes for clarity
    const device_hash = await sha256(navigator.platform + screen.width + screen.height);
    const browser_hash = await sha256(navigator.userAgent);
    const os_hash = await sha256(navigator.platform);

    return {
        fingerprint_id,
        device_hash,
        browser_hash,
        os_hash
    };
}

// Auto-send to backend
async function sendFingerprint() {
    const fp = await generateFingerprint();

    const ip = await fetch("https://api.ipify.org?format=json")
        .then(r => r.json())
        .then(j => j.ip)
        .catch(() => null);

    await fetch("https://mylocal.quailtv.org/api/fingerprint/store", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            fingerprint_id: fp.fingerprint_id,
            device_hash: fp.device_hash,
            browser_hash: fp.browser_hash,
            os_hash: fp.os_hash,
            ip_address: ip
        })
    });
}

sendFingerprint();
