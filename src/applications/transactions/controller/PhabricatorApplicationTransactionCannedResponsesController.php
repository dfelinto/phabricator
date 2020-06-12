<?php

final class PhabricatorApplicationTransactionCannedResponsesController
  extends PhabricatorApplicationTransactionController {

  public function shouldAllowPublic() {
    return true;
  }

  public function handleRequest(AphrontRequest $request) {
    $viewer = $request->getViewer();

    $phid = head($request->getArr('macro'));
    $reply = $request->getStr('reply');

    $e_macro = true;
    $errors = array();
    if ($request->isDialogFormPost()) {

      if (!$errors) {
        $result = array(
          'reply' => $reply,
        );
        return id(new AphrontAjaxResponse())->setContent($result);
      }
    }

    $responses = json_decode($request->getStr('responses'));

    $view = id(new AphrontFormView())
      ->setUser($viewer)
      ->appendControl(
          id(new AphrontFormSelectControl())
            ->setName('reply')
            ->setOptions($this->getCannedResponsesMap($responses))
          );

    $dialog = id(new AphrontDialogView())
      ->setUser($viewer)
      ->setTitle(pht("Canned Responses"))
      ->appendForm($view)
      ->addCancelButton('/')
      ->addSubmitButton(pht('Add Text'));

    return id(new AphrontDialogResponse())->setDialog($dialog);
  }

  private static function getCannedResponsesMap($responses) {
    $map = array();
    foreach ($responses as $key => $value) {
      $map[$value->{"message"}] = pht($value->{"name"});
    }
    return $map;
  }
}
