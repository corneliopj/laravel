@php
    $pageTitle = 'Certidão de Registro de Ave';
@endphp

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <style>
        body {
            font-family: 'Inter', sans-serif; /* Usando Inter como padrão */
            margin: 0;
            padding: 20px;
            font-size: 90%; /* Reduz todas as fontes em 10% */
            line-height: 1.5; /* Ajuste para melhor legibilidade */
            color: #222; /* Cor de texto padrão mais escura */
        }
        .certidao-container {
            width: 21cm; /* Largura para simular uma folha A4 */
            min-height: 29.7cm; /* Altura para simular uma folha A4 */
            margin: 20px auto;
            border: 1px solid #FFD700; /* Borda dourada */
            padding: 35px; /* Aumenta um pouco o padding */
            box-shadow: 0 4px 12px rgba(0,0,0,0.15); /* Sombra mais pronunciada */
            background-color: #ffffff;
            border-radius: 8px; /* Cantos arredondados */
        }
        .header {
            text-align: center;
            margin-bottom: 35px; /* Mais espaço abaixo do header */
        }
        .header img {
            max-width: 105px; /* Reduz o logo para 70% do original (150px * 0.7 = 105px) */
            height: auto;
            margin-bottom: 15px; /* Mais espaço abaixo do logo */
            border-radius: 6px; /* Cantos arredondados para o logo */
            display: block; /* Garante que a imagem esteja em sua própria linha */
            margin-left: auto;
            margin-right: auto;
        }
        .header h1 {
            font-size: 2.2em; /* Ajuste proporcional */
            margin: 0;
            color: #000; /* Título em preto */
            text-transform: uppercase; /* Deixa o título em maiúsculas */
            letter-spacing: 1px;
            border-bottom: 2px solid #FFD700; /* Linha dourada sob o título */
            padding-bottom: 10px;
            display: inline-block; /* Para a borda aplicar apenas ao texto */
        }
        .main-content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr; /* Duas colunas de largura igual */
            gap: 40px; /* Espaço entre as colunas */
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 0; /* Remover margin-bottom aqui para o grid controlar */
            border-bottom: none; /* Remover borda inferior aqui */
            padding-bottom: 0; /* Remover padding inferior aqui */
        }
        .section h2 {
            font-size: 1.6em; /* Ajuste proporcional */
            color: #FFD700; /* Cor dourada para os subtítulos */
            margin-bottom: 18px;
            padding-bottom: 8px;
            border-bottom: 1px solid #000; /* Borda sólida preta para subtítulos */
            font-weight: 700; /* Mais peso */
        }
        .info-row {
            margin-bottom: 8px; /* Mais espaço entre as linhas de informação */
            display: flex; /* Usar flexbox para alinhamento */
            align-items: flex-start; /* Alinha o início do texto */
        }
        .info-row strong {
            display: inline-block;
            width: 130px; /* Ajuste para alinhar os rótulos em 2 colunas */
            color: #000; /* Rótulos em preto */
            font-weight: bold;
            flex-shrink: 0; /* Impede o encolhimento do rótulo */
            padding-right: 10px;
        }
        .info-row span {
            color: #333; /* Conteúdo da informação em cinza escuro */
            flex-grow: 1; /* Permite que o conteúdo ocupe o espaço restante */
        }
        .validation-section {
            display: flex;
            justify-content: space-between; /* Espaça o código de validação e o QR Code */
            align-items: center;
            gap: 40px; /* Espaço entre os elementos */
            margin-top: 40px;
            padding: 20px;
            background-color: #fcf8e3; /* Fundo suave para a seção de validação */
            border: 2px dashed #FFD700; /* Borda dourada tracejada */
            border-radius: 8px;
        }
        .validation-code {
            text-align: center;
            font-size: 1.1em; /* Ajuste proporcional */
            font-weight: bold;
            color: #000; /* Texto em preto */
            letter-spacing: 0.8px;
            flex-grow: 1; /* Permite que o código de validação ocupe mais espaço */
        }
        .footer {
            text-align: center;
            margin-top: 50px; /* Mais espaço acima do footer */
            font-size: 0.85em; /* Ajuste proporcional */
            color: #333; /* Texto do footer em cinza escuro */
            border-top: 1px solid #FFD700; /* Borda dourada no topo do footer */
            padding-top: 15px;
        }
        .footer .warning {
            color: #8B0000; /* Cor para o aviso */
            font-weight: bold;
            margin-bottom: 10px;
        }
        .footer .address {
            margin-top: 10px;
            line-height: 1.4;
        }
        /* Estilos para campos N/A */
        .info-row span.na {
            color: #888;
            font-style: italic;
        }

        /* Estilo para o contêiner do QR Code */
        #qrcode-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex-shrink: 0; /* Impede o encolhimento do QR Code */
        }
        #qrcode-container p {
            font-size: 0.9em;
            margin-top: 5px;
            color: #555;
        }

        /* Estilos para o botão de impressão */
        .print-button-container {
            text-align: center;
            margin-top: 20px;
        }
        .print-button {
            background-color: #000; /* Fundo preto */
            color: #FFD700; /* Texto dourado */
            border: 1px solid #FFD700; /* Borda dourada */
            padding: 10px 20px;
            font-size: 1.1em;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .print-button:hover {
            background-color: #FFD700; /* Fundo dourado no hover */
            color: #000; /* Texto preto no hover */
        }

        /* Estilos para a foto da ave */
        .bird-photo {
            width: 150px; /* Tamanho fixo para a foto/silhueta */
            height: 150px; /* Tamanho fixo para a foto/silhueta */
            border: 2px solid #FFD700; /* Borda dourada ao redor da foto */
            border-radius: 8px; /* Cantos arredondados */
            margin: 0 auto 20px auto; /* Centraliza e adiciona margem inferior */
            display: block; /* Garante que a imagem seja um bloco para margin auto */
            box-shadow: 0 2px 8px rgba(0,0,0,0.1); /* Sombra suave */
            object-fit: contain; /* Garante que a imagem se ajuste ao contêiner sem cortar */
            background-color: #f8f8f8; /* Fundo claro para as silhuetas */
        }

        /* Oculta o botão de impressão quando estiver no modo de impressão */
        @media print {
            .print-button-container {
                display: none;
            }
        }
    </style>
    <!-- Inclui a biblioteca qrcode.js para gerar o QR Code -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
    <div class="certidao-container">
        <div class="header">
            {{-- Caminho do logo corrigido para {{ asset('img/logo.png') }} --}}
            {{-- Adicionado onerror para exibir um placeholder se a imagem não for encontrada. --}}
            <img src="{{ asset('img/logo.png') }}"
                 alt="Logo do Criatório"
                 onerror="this.onerror=null; this.src='https://placehold.co/105x105/000000/FFD700?text=LOGO';">
            <h1>CERTIDÃO DE REGISTRO DE AVE</h1>
        </div>

        <div class="main-content-grid">
            <div class="section">
                <h2>Dados da Ave</h2>
                {{-- Lógica para exibir a foto ou a silhueta da ave --}}
                @if ($ave->foto_path)
                    {{-- O caminho foi corrigido para usar asset() diretamente com o caminho armazenado --}}
                    <img src="{{ asset($ave->foto_path) }}"
                         alt="Foto da Ave"
                         class="bird-photo"
                         onerror="this.onerror=null; this.src='https://placehold.co/150x150/e0e0e0/555555?text=Erro%20Foto';">
                @else
                    @php
                        $birdTypeName = strtolower($ave->tipoAve->nome ?? ''); // Obtém o nome do tipo de ave em minúsculas
                        $silhouetteFileName = '';

                        // Define o nome do arquivo da silhueta com base no tipo de ave
                        // Usamos um array de mapeamento para facilitar a gestão e evitar um switch complexo
                        $silhouetteMap = [
                            'galinaceos' => 'galinha.png',
                            'perus'      => 'peru.png',
                            'marrecos'   => 'mareco.png',
                            'angolas'    => 'angola.png',
                            'codornas'   => 'codorna.png',
                            'gansos'     => 'ganso.png', // NOVO: Adicionado gansos
                            // Adicione outros tipos de aves e seus respectivos nomes de arquivo aqui
                        ];

                        $silhouetteFileName = $silhouetteMap[$birdTypeName] ?? null; // Obtém o nome do arquivo ou null se não houver correspondência

                        $silhouetteSrc = $silhouetteFileName ? asset('img/' . $silhouetteFileName) : null;
                        
                        // Gerar URL do placeholder dinamicamente se a imagem local não for encontrada
                        $placeholderText = ucfirst($birdTypeName) ?: 'Ave'; // Capitaliza o nome para o texto do placeholder
                        $genericPlaceholderSrc = 'https://placehold.co/150x150/e0e0e0/000000?text=' . urlencode($placeholderText);
                    @endphp
                    <img src="{{ $silhouetteSrc }}"
                         alt="Silhueta da Ave"
                         class="bird-photo"
                         onerror="this.onerror=null; this.src='{{ $genericPlaceholderSrc }}';">
                @endif
                <p class="info-row"><strong>Matrícula:</strong> <span>{{ $ave->matricula ?? 'N/A' }}</span></p>
                <p class="info-row"><strong>Tipo de Ave:</strong> <span>{{ $ave->tipoAve->nome ?? 'N/A' }}</span></p>
                <p class="info-row"><strong>Variação:</strong> <span>{{ $ave->variacao->nome ?? 'N/A' }}</span></p>
                <p class="info-row"><strong>Sexo:</strong> <span>{{ $ave->sexo ?? 'N/A' }}</span></p>
                <p class="info-row"><strong>Data de Eclosão:</strong> <span>{{ $ave->data_eclosao ? $ave->data_eclosao->format('d/m/Y') : 'N/A' }}</span></p>
                <p class="info-row"><strong>Status:</strong> <span>{{ ($ave->morte) ? 'Morto' : (($ave->ativo ?? 1) ? 'Ativo' : 'Inativo') }}</span></p>
                @if ($ave->morte)
                    <p class="info-row"><strong>Data da Morte:</strong> <span>{{ $ave->morte->data_morte->format('d/m/Y') }}</span></p>
                    <p class="info-row"><strong>Causa da Morte:</strong> <span>{{ $ave->morte->causa ?? 'Não informada' }}</span></p>
                @endif
            </div>

            <div class="section">
                <h2>Origem da Incubação</h2>
                {{-- Adicionado operador null-safe '?' para lidar com ave->incubacao nula --}}
                <p class="info-row"><strong>Lote:</strong> <span>{{ $ave->lote?->identificacao_lote ?? 'N/A' }}</span></p>
                <p class="info-row"><strong>Incubação ID:</strong> <span>{{ $ave->incubacao?->id ?? 'N/A' }}</span></p>
                <p class="info-row"><strong>Data Entrada Incubadora:</strong> <span>{{ $ave->incubacao?->data_entrada_incubadora?->format('d/m/Y') ?? 'N/A' }}</span></p>
                <p class="info-row"><strong>Quantidade Ovos Incubados:</strong> <span>{{ $ave->incubacao?->quantidade_ovos ?? 'N/A' }}</span></p>
                <p class="info-row"><strong>Quantidade Eclodidos:</strong> <span>{{ $ave->incubacao?->quantidade_eclodidos ?? 'N/A' }}</span></p>

                {{-- Seção de Filiação (visível apenas se houver dados de acasalamento) --}}
                @if ($ave->incubacao && $ave->incubacao->posturaOvo && $ave->incubacao->posturaOvo->acasalamento)
                    @php
                        $acasalamento = $ave->incubacao->posturaOvo->acasalamento;
                        $macho = $acasalamento->macho;
                        $femea = $acasalamento->femea;
                    @endphp
                    <h2 class="mt-4">Filiação</h2>
                    <p class="info-row">
                        <strong>Filiação:</strong>
                        <span>
                            @if($macho) {{ $macho->matricula }} @else N/A @endif
                            e
                            @if($femea) {{ $femea->matricula }} @else N/A @endif
                        </span>
                    </p>
                    <p class="info-row"><strong>Acasalamento ID:</strong> <span>{{ $acasalamento->id ?? 'N/A' }}</span></p>
                @endif
            </div>
        </div> {{-- Fim do main-content-grid --}}

        <div class="validation-section">
            <div class="validation-code">
                Código de Validação: {{ $ave->validation_code ?? 'N/A' }}
            </div>
            <div id="qrcode-container">
                <div id="qrcode" style="width:100px; height:100px;"></div>
                <p>Verifique a autenticidade</p>
            </div>
        </div>

        <div class="footer">
            <div class="warning">
                <strong>ADVERTÊNCIA:</strong> Esta certidão é um documento oficial. Qualquer alteração ou falsificação será tratada conforme a lei.
            </div>
            <div class="address">
                Criatório Coroné <br>
                Linha 176, km 3.5 - Norte <br>
                Santa Luzia dOeste, Rondônia - CEP 76.950-000 <br>
                Telefone: (69) 99996-0942 | E-mail: contato@criatorio.com.br
            </div>
            Certidão emitida em: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
        </div>
    </div>

    <div class="print-button-container">
        <button class="print-button" onclick="window.print()">Imprimir Certidão</button>
    </div>

    <script type="text/javascript">
        window.onload = function() {
            var qrCodeDiv = document.getElementById("qrcode");
            if (qrCodeDiv) {
                var validationCode = "{{ $ave->validation_code ?? '' }}";
                if (validationCode) {
                    var qrCodeUrl = "{{ route('certidao.show', ['validation_code' => ($ave->validation_code ?? '')], true) }}";
                    new QRCode(qrCodeDiv, {
                        text: qrCodeUrl,
                        width: 100,
                        height: 100,
                        colorDark : "#000000", /* Cor escura do QR Code */
                        colorLight : "#ffffff", /* Cor clara do QR Code (fundo) */
                        correctLevel : QRCode.CorrectLevel.H /* Nível de correção de erros alto */
                    });
                } else {
                    qrCodeDiv.style.display = 'none';
                    document.querySelector('#qrcode-container p').textContent = 'QR Code indisponível';
                }
            }
        };
    </script>
</body>
</html>
