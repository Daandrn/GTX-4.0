<?php declare(strict_types=1);

namespace App\Services;

use App\DTO\UpdateCanalStreamDTO;
use App\Helpers\SanitizeInput;
use App\Repositories\CanalStreamRepository;

class CanalStreamService
{
    
    public function __construct(
        protected CanalStreamRepository $canalStreamRepository,
    ) {
        //
    }

    public function newStream(int $id): array
    {
        $exists = $this->canalStreamRepository->getStream($id);
        
        if ($exists->isNotEmpty()) {
            return ['message' => "Erro: Já existe cadastro com este id. verifique!"];
        }

        $wasCreated = $this->canalStreamRepository->new($id);

        if (!$wasCreated) {
            return ['message' => "Erro ao criar canal de stream. verifique!"];
        }

        return ['message' => "Canal de stream criado com sucesso!"];
    }

    public function updateStream(UpdateCanalStreamDTO $dto): array
    {
        if (preg_match('[\'"<>&;/\|]', $dto->nick_stream)) {
            return ['message' => "O campo nick stream não pode conter caracteres especiais!"];
        }

        if (preg_match('[\'"<>&;/\|]', $dto->link_canal)) {
            return ['message' => "O campo link do canal não pode conter caracteres especiais!"];
        }
        
        $dto->nick_stream = SanitizeInput::make($dto->nick_stream);
        $dto->link_canal  = SanitizeInput::make($dto->link_canal);

        if (!strlen($dto->nick_stream) > 0) {
            return ['message' => "O campo nick stream é de preenchimento obrigatório!"];
        }

        if (!strlen($dto->link_canal) > 0) {
            return ['message' => "O campo link do canal é de preenchimento obrigatório!"];
        }
        
        if (is_null($dto->plataforma)) {
            return ['message' => "O campo plataforma é de preenchimento obrigatório!"];
        }

        if (!is_numeric($dto->plataforma)) {
            return ['message' => "O campo plataforma é inválido!"];
        }

        if (strlen($dto->nick_stream) > 20) {
            return ['message' => "O campo nick deve ter no maximo 20 caracteres!"];
        }

        if (strlen($dto->link_canal) > 50) {
            return ['message' => "O campo link do canal deve ter no maximo 50 caracteres!"];
        }

        $streamExists = $this->canalStreamRepository->getStream($dto->membro_id);

        if ($streamExists->isEmpty()) {
            return ['message' => "Não existe canal de stream para o usuário. Procure um administrador!"];
        }

        if ($dto->link_canal) {
            $dto->link_canal = $this->linkFormat($dto->link_canal);
        }
        
        $wasUpdated = $this->canalStreamRepository->update($dto);

        if (!$wasUpdated) {
            return ['message' => "Erro ao alterar canal de stream. Procure um administrador!"];
        }

        return ['message' => "Canal de stream alterado com sucesso!"];
    }

    public function limpaStream(UpdateCanalStreamDTO $dto): array
    {
        $streamExists = $this->canalStreamRepository->getStream($dto->membro_id);

        if ($streamExists->isEmpty()) {
            return ['message' => "Não existe canal de stream para o usuário. Procure um administrador!"];
        }
        
        $wasUpdated = $this->canalStreamRepository->update($dto);

        if (!$wasUpdated) {
            return ['message' => "Erro ao limpar canal de stream. Procure um administrador!"];
        }
        
        return ['message' => "Canal de stream excluído com sucesso!"];
    }

    public function deleteStream(int $id): array
    {
        $this->canalStreamRepository->delete($id);
        
        return ['message' => "Canal de stream excluído com sucesso!"];
    }

    protected function linkFormat(string $link) 
    {
        return str_ireplace(['www.', 'https://', 'http://', ' '], '', $link);
    }
}
