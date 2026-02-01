package org.example.translatorresource;

import jakarta.ws.rs.Consumes;
import jakarta.ws.rs.POST;
import jakarta.ws.rs.Path;
import jakarta.ws.rs.Produces;
import jakarta.ws.rs.core.MediaType;
import jakarta.ws.rs.core.Response;

@Path("/translator")
public class TranslatorResource {

    private GroqLLMService llmService = new GroqLLMService();
    @POST
    @Consumes(MediaType.APPLICATION_JSON) 
    @Produces(MediaType.APPLICATION_JSON)
    public Response translate(TranslationRequest request) {
        if (request == null || request.getText() == null) {
            return Response.status(Response.Status.BAD_REQUEST)
                    .entity("{\"error\": \"JSON body must contain 'text' field\"}").build();
        }
        try {
            String translation = llmService.translateToDarija(request.getText());
            return Response.ok(translation).build();
        } catch (Exception e) {
            return Response.serverError().entity(e.getMessage()).build();
        }
    }
}
