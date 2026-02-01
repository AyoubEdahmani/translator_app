package org.example.translatorresource;

import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;

public class GroqLLMService {
    private static final String API_KEY = "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";
    private static final String API_URL = "https://api.groq.com/openai/v1/chat/completions";

    public String translateToDarija(String englishText) throws Exception {
        String jsonPayload = "{"
                + "\"model\": \"openai/gpt-oss-120b\","
                + "\"messages\": ["
                + "  {\"role\": \"system\", \"content\": \"You are a professional translator. Translate English text to Moroccan Darija only.\"},"
                + "  {\"role\": \"user\", \"content\": \"" + englishText + "\"}"
                + "]"
                + "}";

        HttpClient client = HttpClient.newHttpClient();
        HttpRequest request = HttpRequest.newBuilder()
                .uri(URI.create(API_URL))
                .header("Authorization", "Bearer " + API_KEY)
                .header("Content-Type", "application/json")
                .POST(HttpRequest.BodyPublishers.ofString(jsonPayload))
                .build();

        HttpResponse<String> response = client.send(request, HttpResponse.BodyHandlers.ofString());
        return response.body();
    }
}