<?php

declare(strict_types=1);

namespace Drupal\my_movies\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form for a movie entity type.
 */
final class MovieSettingsForm extends FormBase
{

  /**
   * Unique form id
   * 
   * @var string
   */
  const FORM_ID = "my_movies_movie_settings";

  /**
   * Config settings.
   * The name of the configuration object to retrieve. The name corresponds to a configuration file.
   * {name}.yml
   * We'll be adding: web/modules/custom/my_movies/config/install/my_movies.settings.yml
   * 
   * my_movies = machine name of custom module
   * settings = refers to values used for settings or anything else we wish it to be. Examples include maintenance, mailer, cron, etc.
   * 
   * @var string
   */
  const SETTINGS = "my_movies.settings";

  /**
   * Setting key for Language Options
   * this is an important structure and is used by the config install setting yaml file.
   * movie:
   *   languages: "list of values"
   * 
   * This structure is also important in defining the config object schema. 
   * see: web/modules/custom/my_movies/config/schema/my_movies.schema.yml
   * 
   * @var string
   */
  const SETTING_KEY_MOVIE_LANGUAGES = "movie.languages";

  /**
   * The form input name for languages.
   * 
   */
  const FORM_INPUT_NAME_LANGUAGES = "languages";

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string
  {
    return static::FORM_ID;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array
  {

    $config = $this->config(static::SETTINGS);

    $form['settings'] = [
      '#markup' => $this->t('Settings form for a movie entity type.'),
    ];

    $languageOptionsDescription = $this->getLanguageOptionsDescription();

    $form[static::FORM_INPUT_NAME_LANGUAGES] = [
      "#type" => "textarea",
      "#title" => $this->t("Language Options"),
      # the get() will try to retrive previously saved setting for language options.
      "#default_value"  => $config->get(static::SETTING_KEY_MOVIE_LANGUAGES) ?? "en|English\r\nes|Spanish\r\ntl|Tagalog",
      "#description" => $this->t($languageOptionsDescription)
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Save'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void
  {
    $config = $this->configFactory()->getEditable(static::SETTINGS);

    $config->set(static::SETTING_KEY_MOVIE_LANGUAGES, $form_state->getValue(static::FORM_INPUT_NAME_LANGUAGES));

    // save the configuration
    $config->save();

    $this->messenger()->addStatus($this->t('The configuration has been updated.'));
  }

  /**
   * Get a description for the language options configuration setting
   *
   * @return string
   */
  public function getLanguageOptionsDescription(): string
  {
    $tags = htmlentities("<a> <b> <big> <code> <del> <em> <i> <ins> <pre> <q> <small> <span> <strong> <sub> <sup> <tt> <ol> <ul> <li> <p> <br> <img>");

    $description = <<<EOD
<p>
  The possible values this field can contain. Enter one value per line, in the format key|label.<br />
  The key is the stored value. The label will be used in displayed values and edit forms.<br />
  The label is optional: if a line contains a single string, it will be used as key and label.<br /><br />  
  Allowed HTML tags in labels: {$tags}
</p>
EOD;

    return $description;
  }
}
