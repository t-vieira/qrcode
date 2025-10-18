<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SupportTicket;

class SupportTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(10)->get();

        $subjects = [
            'Problema com geração de QR Code',
            'Dúvida sobre assinatura',
            'Erro no dashboard',
            'Como personalizar QR Code',
            'Problema com pagamento',
            'Dúvida sobre estatísticas',
            'Erro ao baixar QR Code',
            'Como compartilhar QR Code',
            'Problema com domínio customizado',
            'Dúvida sobre equipes',
        ];

        $messages = [
            'Olá, estou tendo problemas para gerar um QR Code. O sistema não está respondendo.',
            'Gostaria de saber mais informações sobre os planos de assinatura disponíveis.',
            'O dashboard não está carregando corretamente. Pode me ajudar?',
            'Como posso personalizar as cores do meu QR Code?',
            'Tentei fazer o pagamento mas deu erro. O que posso fazer?',
            'Não consigo ver as estatísticas dos meus QR Codes. Está tudo certo?',
            'Quando tento baixar o QR Code, o arquivo não é gerado.',
            'Como posso compartilhar meu QR Code nas redes sociais?',
            'Configurei meu domínio customizado mas não está funcionando.',
            'Como posso adicionar membros à minha equipe?',
        ];

        $statuses = ['open', 'pending', 'closed', 'resolved'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $categories = ['technical', 'billing', 'feature_request', 'bug_report', 'general', 'account', 'subscription'];

        foreach ($users as $user) {
            // Criar 1-3 tickets por usuário
            $ticketCount = rand(1, 3);
            
            for ($i = 0; $i < $ticketCount; $i++) {
                $subject = $subjects[array_rand($subjects)];
                $message = $messages[array_rand($messages)];
                $status = $statuses[array_rand($statuses)];
                $priority = $priorities[array_rand($priorities)];
                $category = $categories[array_rand($categories)];

                $ticket = SupportTicket::create([
                    'user_id' => $user->id,
                    'subject' => $subject,
                    'message' => $message,
                    'status' => $status,
                    'priority' => $priority,
                    'category' => $category,
                    'created_at' => now()->subDays(rand(0, 30)),
                ]);

                // Se o ticket está fechado, definir last_reply_at
                if (in_array($status, ['closed', 'resolved'])) {
                    $ticket->update([
                        'last_reply_at' => now()->subDays(rand(0, 15)),
                    ]);
                }
            }
        }

        // Criar alguns tickets adicionais com diferentes status
        $additionalUsers = User::skip(10)->take(5)->get();
        
        foreach ($additionalUsers as $user) {
            // Ticket urgente
            SupportTicket::create([
                'user_id' => $user->id,
                'subject' => 'URGENTE: Sistema fora do ar',
                'message' => 'O sistema está completamente fora do ar. Não consigo acessar nenhuma funcionalidade.',
                'status' => 'open',
                'priority' => 'urgent',
                'category' => 'technical',
                'created_at' => now()->subHours(2),
            ]);

            // Ticket de solicitação de funcionalidade
            SupportTicket::create([
                'user_id' => $user->id,
                'subject' => 'Solicitação: Suporte a mais tipos de QR Code',
                'message' => 'Gostaria de sugerir a adição de novos tipos de QR Code, como para redes sociais e aplicativos.',
                'status' => 'pending',
                'priority' => 'medium',
                'category' => 'feature_request',
                'created_at' => now()->subDays(5),
            ]);

            // Ticket de bug
            SupportTicket::create([
                'user_id' => $user->id,
                'subject' => 'Bug: QR Code não gera com logo',
                'message' => 'Quando tento adicionar um logo ao QR Code, o sistema retorna erro 500.',
                'status' => 'resolved',
                'priority' => 'high',
                'category' => 'bug_report',
                'created_at' => now()->subDays(10),
                'last_reply_at' => now()->subDays(8),
            ]);
        }
    }
}
