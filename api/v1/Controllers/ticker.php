<?php
class Controller_Ticker extends RestController {

    public function RequiredScope($method) {
        return ScopeData::TICKER;
    }

    public function GET() {
        if (!isset($this->args[0])) {
            $this->AddError(APIErrorCodes::PARAM_DAY_MISSING);
            return;
        }

        $type = RestUtil::CheckUserType($this->user);

        if ($type === false)
            return;

        $replacements = RestUtil::GetReplacements($type, $this->args[0]);

        if ($replacements === null)
            return;

        $tickers = $replacements->GetTickers();

        $this->meta = Array('next'=>  RestUtil::GetNextTFrom($replacements->GetDate()));
        $this->response = $tickers;
        $this->responseStatus = 200;
    }
}
?>