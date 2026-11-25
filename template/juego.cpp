#include <iostream>
#include <cstdlib>
#include <ctime>

int main() {
    // Inicializar la semilla para números aleatorios
    std::srand(static_cast<unsigned int>(std::time(0)));

    int salud = 100;
    int balas = 12;
    int botiquines = 2;
    int oleada = 1;
    int maxOleadas = 5;

    std::cout << "=== SUPERVIVENCIA ZOMBIE ===" << std::endl;
    std::cout << "Sobrevive a " << maxOleadas << " oleadas para ser rescatado.\n" << std::endl;

    while (salud > 0 && oleada <= maxOleadas) {
        int zombies = oleada * 2 + (std::rand() % 3); // Cantidad de zombies aumenta por oleada
        std::cout << "\n--- OLEADA " << oleada << " ---" << std::endl;
        std::cout << "¡Aparecen " << zombies << " zombies!" << std::endl;

        while (zombies > 0 && salud > 0) {
            std::cout << "\n[Estado] Salud: " << salud << " | Balas: " << balas 
                      << " | Botiquines: " << botiquines << " | Zombies restantes: " << zombies << std::endl;
            
            std::cout << "Elige tu accion:" << std::endl;
            std::cout << "1. Disparar (gasta 1 bala, elimina 1 zombie)" << std::endl;
            std::cout << "2. Ataque cuerpo a cuerpo (no gasta balas, riesgo de recibir daño)" << std::endl;
            std::cout << "3. Usar botiquin (+35 de salud)" << std::endl;
            std::cout << "4. Buscar suministros (puedes encontrar balas o recibir un ataque)" << std::endl;
            std::cout << "Opcion: ";
            
            int opcion;
            std::cin >> opcion;

            // Validar que la entrada sea un número
            if (std::cin.fail()) {
                std::cin.clear();
                std::cin.ignore(10000, '\n');
                std::cout << "Entrada invalida. Pierdes el turno por dudar." << std::endl;
                opcion = 0;
            }

            std::cout << std::endl;

            if (opcion == 1) {
                if (balas > 0) {
                    balas--;
                    zombies--;
                    std::cout << "¡PUM! Eliminaste a un zombie de un disparo." << std::endl;
                } else {
                    std::cout << "¡Click! No tienes balas. Los zombies aprovechan para atacarte." << std::endl;
                }
            } 
            else if (opcion == 2) {
                int suerte = std::rand() % 100;
                if (suerte < 65) {
                    zombies--;
                    std::cout << "¡Golpe certero! Eliminaste a un zombie con tu cuchillo." << std::endl;
                } else {
                    std::cout << "¡Fallaste el golpe y quedaste expuesto!" << std::endl;
                }
            } 
            else if (opcion == 3) {
                if (botiquines > 0) {
                    botiquines--;
                    salud += 35;
                    if (salud > 100) salud = 100;
                    std::cout << "Usaste un botiquin. Tu salud subio a " << salud << "." << std::endl;
                } else {
                    std::cout << "No te quedan botiquines." << std::endl;
                }
            } 
            else if (opcion == 4) {
                int hallazgo = std::rand() % 100;
                if (hallazgo < 50) {
                    int balasEncontradas = 3 + (std::rand() % 4);
                    balas += balasEncontradas;
                    std::cout << "¡Suerte! Encontraste " << balasEncontradas << " balas en el suelo." << std::endl;
                } else if (hallazgo < 75) {
                    botiquines++;
                    std::cout << "¡Genial! Encontraste un botiquin." << std::endl;
                } else {
                    std::cout << "No encontraste nada util y perdiste tiempo." << std::endl;
                }
            } 
            else {
                std::cout << "No hiciste nada util este turno." << std::endl;
            }

            // Turno de ataque de los zombies si aún queda alguno vivo
            if (zombies > 0) {
                int probabilidadAtaque = std::rand() % 100;
                // Si disparaste y acertaste, es menos probable que te ataquen en ese instante
                if (opcion == 1 && probabilidadAtaque < 40) {
                    std::cout << "Los zombies restantes dudan ante el ruido del disparo." << std::endl;
                } else {
                    int dano = (std::rand() % 10 + 5) * zombies; // El daño escala según cuántos zombies queden
                    if (dano > 35) dano = 35; // Límite de daño por turno para balancear
                    salud -= dano;
                    std::cout << "¡Los zombies te atacan! Recibes " << dano << " puntos de dano." << std::endl;
                }
            }
        }

        if (salud > 0) {
            std::cout << "\n¡Has despejado la oleada " << oleada << "!" << std::endl;
            int balasBonus = 3;
            balas += balasBonus;
            std::cout << "Encuentras " << balasBonus << " balas entre los restos antes de la siguiente oleada." << std::endl;
            oleada++;
        }
    }

    std::cout << "\n=============================" << std::endl;
    if (salud > 0) {
        std::cout << "¡FELICIDADES! Llego el helicoptero de rescate. Sobreviviste." << std::endl;
    } else {
        std::cout << "HAS MUERTO. Los zombies te superaron en la oleada " << oleada << "." << std::endl;
    }
    std::cout << "=============================" << std::endl;

    return 0;
}
