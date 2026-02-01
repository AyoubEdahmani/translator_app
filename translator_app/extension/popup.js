document.getElementById('translateBtn').addEventListener('click', async () => {
    const rawText = document.getElementById('inputText').value;
    const text = rawText.replace(/(\r\n|\n|\r)/gm, " "); 

    const resultDiv = document.getElementById('result');
    const errorDiv = document.getElementById('error');

    resultDiv.style.display = 'none';
    errorDiv.style.display = 'none';

    if (!text.trim()) {
        showError("Please enter some text first.");
        return;
    }

    try {
        const translateBtn = document.getElementById('translateBtn');
        
        translateBtn.innerText = "Translating...";
        translateBtn.disabled = true;

        const response = await fetch('http://localhost:8080/TranslatorResource-1.0-SNAPSHOT/api/translator', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic ' + btoa('admin:password') 
            },
            body: JSON.stringify({ text: text })
        });

        if (!response.ok) {
            if (response.status === 401) throw new Error("Authentication Failed (Check admin:password)");
            const errorData = await response.json().catch(() => ({})); 
            throw new Error(errorData.error?.message || `Server Error: ${response.status}`);
        }

        const data = await response.json();
        
        let translatedText = "";
        
        if (data.choices && data.choices[0] && data.choices[0].message) {
            translatedText = data.choices[0].message.content;
        } else if (data.translation) {
            translatedText = data.translation;
        } else {
            translatedText = JSON.stringify(data); 
        }

        resultDiv.innerText = translatedText;
        resultDiv.style.display = 'block';

    } catch (err) {
        showError(err.message);
    } finally {
        document.getElementById('translateBtn').innerText = "Translate Now";
        document.getElementById('translateBtn').disabled = false;
    }
});

function showError(msg) {
    const errorDiv = document.getElementById('error');
    errorDiv.innerText = msg;
    errorDiv.style.display = 'block';
}