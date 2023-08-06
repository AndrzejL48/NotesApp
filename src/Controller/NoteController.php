<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\NotesModel;
use App\Request;

class NoteController extends AbstractController
{
    private const DEFAULT_ACTION = 'list';

    protected NotesModel $notesModel;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->defaultAction = self::DEFAULT_ACTION;
        $this->notesModel = new NotesModel();
    }
    public function listAction()
    {
        $sortBy = null;
        $sortOrder = null;
        $pageNumber = null;
        $search = null;

        if ($this->request->isGetRequest() && $this->request->isGetSend()) {
            $search = $this->request->getParam('search', $this->session->getLocalSessionVariable('search'));
            $sortBy = $this->request->getParam('sort_by', $this->session->getLocalSessionVariable('sort_by'));
            $sortOrder = $this->request->getParam('sort_order', $this->session->getLocalSessionVariable('sort_order'));
            $pageNumber = $this->request->getParam('page_number');

            if (!empty($search)) {
                $this->notesModel->setSearch('title', $search);
                $this->session->setLocalSessionVariable('search', $search);
            }


            if (!empty($sortBy) && !empty($sortOrder)) {
                $this->notesModel->setSort($sortBy, $sortOrder);
                $this->session->setLocalSessionVariable('sort_by', $sortBy);
                $this->session->setLocalSessionVariable('sort_order', $sortOrder);
            }
        } else {
            $this->session->killSession();
        }

        $this->notesModel->setPageNumber((int) $pageNumber);
        $pagesAmount = $this->notesModel->getAmountOfPages('title', $search);
        $notes = $this->notesModel->getAll();

        $this->viewParams = [
            'notes' => $notes,
            'pagination' => [
                'current_page' => $pageNumber ?? 1,
                'pages_amount' => $pagesAmount,
            ],
            'search' => $search ?? '',
            'before' => $this->request->getParam('before'),
            'error' => $this->request->getParam('error')
        ];
    }

    public function showAction()
    {
        $note = $this->getNoteData();

        $this->viewParams = [
            'note' => $note,
            'before' => $this->request->getParam('before')
        ];
    }

    public function createAction()
    {
        if ($this->request->isPostSend() && $this->request->isPostRequest()) {
            $data = [
                'title' => $this->request->postParam('title'),
                'description' => $this->request->postParam('description'),
                'created' => date('Y-m-d H:i:s')
            ];

            $isValid = $this->validator->validate(
                'valueNotEmpty',
                $data
            );

            if ($isValid) {
                $redirectParams = [
                    'before' => 'created'
                ];

                $this->notesModel->create($data);
                $this->redirect($redirectParams);
            } else {
                $invalidElements = $this->validator->getInvalidElements(false);
                $this->viewParams = [
                    'error' => 'emptyRequiredFields',
                    'error_message' => "Required fields cannot be empty $invalidElements"
                ];
            }
        }
    }

    public function editAction()
    {
        $note = $this->getNoteData();
        $noteId = $note['id'];
        $error = null;

        if ($this->request->isPostRequest() && $this->request->isPostSend()) {
            $newNoteData = [
                'title' => $this->request->postParam('title'),
                'description' => $this->request->postParam('description'),
            ];

            $isValid = $this->validator->validate(
                'valueNotEmpty',
                $newNoteData
            );

            if ($isValid) {
                $target = ['id' => $noteId];
                $note = $newNoteData;

                $this->notesModel->update($note, $target);

                $redirectParams = [
                    'action' => 'show',
                    'before' => 'updated',
                    'id' => $noteId
                ];
                $this->redirect($redirectParams);
            } else {
                $invalidElements = $this->validator->getInvalidElements(false);
                $error = [
                    'error' => 'emptyRequiredFields',
                    'error_message' => "Required fields cannot be empty: $invalidElements"
                ];
            }
        }

        $note = [
            'title' => $note['title'],
            'description' => $note['description']
        ];

        $this->viewParams = [
            'note' => $note,
            'id' => $noteId,
            'error' => $error
        ];
    }

    public function deleteAction()
    {
        $note = $this->getNoteData();
        $target = ['id' => $note['id']];

        if (!empty($note)) {
            $this->notesModel->delete($target);

            $redirectParams = [
                'action' => 'list',
                'before' => 'deleted',
            ];
            $this->redirect($redirectParams);
        }
    }

    private function getNoteData(): array
    {
        $noteId = $this->getNoteId();
        return $this->notesModel->getSingle('id', $noteId);
    }

    private function getNoteId(): int
    {
        $id = (int) $this->request->getParam('id');

        if ($id) {
            return $id;
        }

        $redirectParams = [
            'error' => 'missingNoteId'
        ];

        $this->redirect($redirectParams);
    }
}