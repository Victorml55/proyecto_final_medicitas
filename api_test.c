/*
 * Compilar y ejecutar:
 *   gcc api_test.c -o api_test -lcurl && ./api_test
 *
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <curl/curl.h>

#define BASE_URL "https://medicitas.proyectosweb.celaya.tecnm.mx/api"

/* Acumula la respuesta HTTP en un buffer dinámico */
typedef struct { char *data; size_t size; } Buffer;

static size_t callback(void *ptr, size_t sz, size_t n, void *userdata) {
    size_t bytes = sz * n;
    Buffer *b = (Buffer *)userdata;
    b->data = realloc(b->data, b->size + bytes + 1);
    memcpy(b->data + b->size, ptr, bytes);
    b->size += bytes;
    b->data[b->size] = '\0';
    return bytes;
}

static void imprimir(const char *titulo, long status, const char *cuerpo) {
    printf("\n==================================================\n");
    printf("  %s\n", titulo);
    printf("  Status: %ld\n", status);
    printf("==================================================\n");
    printf("%s\n", cuerpo);
}

/* ── GET todos los consultorios ─────────────────────── */
static void get_consultorios(void) {
    CURL *curl = curl_easy_init();
    Buffer buf = {malloc(1), 0};

    curl_easy_setopt(curl, CURLOPT_URL, BASE_URL "/consultorios.php");
    curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, callback);
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &buf);
    curl_easy_perform(curl);

    long status;
    curl_easy_getinfo(curl, CURLINFO_RESPONSE_CODE, &status);
    imprimir("GET /api/consultorios.php  →  lista todos", status, buf.data);

    curl_easy_cleanup(curl);
    free(buf.data);
}

/* ── POST crear un paciente ─────────────────────────── */
static void post_paciente(void) {
    CURL *curl = curl_easy_init();
    Buffer buf = {malloc(1), 0};

    const char *body =
        "{"
        "\"id_usuario\": 5,"
        "\"numero_expediente\": \"EXP-2026-001\","
        "\"tipo_sangre\": \"O+\","
        "\"alergias\": \"Penicilina\","
        "\"enfermedades_cronicas\": \"Ninguna\","
        "\"contacto_emergencia_nombre\": \"Maria Lopez\","
        "\"contacto_emergencia_telefono\": \"5551234567\","
        "\"seguro_medico\": \"IMSS\","
        "\"numero_poliza\": \"POL-999\""
        "}";

    struct curl_slist *headers = NULL;
    headers = curl_slist_append(headers, "Content-Type: application/json");

    curl_easy_setopt(curl, CURLOPT_URL, BASE_URL "/pacientes.php");
    curl_easy_setopt(curl, CURLOPT_POST, 1L);
    curl_easy_setopt(curl, CURLOPT_POSTFIELDS, body);
    curl_easy_setopt(curl, CURLOPT_HTTPHEADER, headers);
    curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, callback);
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &buf);
    curl_easy_perform(curl);

    long status;
    curl_easy_getinfo(curl, CURLINFO_RESPONSE_CODE, &status);
    imprimir("POST /api/pacientes.php  →  crear paciente", status, buf.data);

    curl_slist_free_all(headers);
    curl_easy_cleanup(curl);
    free(buf.data);
}

int main(void) {
    curl_global_init(CURL_GLOBAL_ALL);
    get_consultorios();
    post_paciente();
    curl_global_cleanup();
    return 0;
}
