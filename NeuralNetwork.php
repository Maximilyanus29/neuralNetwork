<?php

class Neuron
{
    public $weigth;
    public $input = 0;
    public $output;
    public $childs = [];


    public function __construct($weigth)
    {
        $this->weigth = $weigth;
    }

    /*Создаем детей*/
    public function createRelation(Neuron $neuron)
    {
        $this->childs[] = $neuron;
    }

    /*Прибавляем детям (вход * вес) текущего нейрона и прогоняем через функцию сигмоиды, записываем результат в output*/
    public function activateChild()
    {
        foreach ($this->childs as $child){
            $child->input += $this->weigth * $this->input;
            $child->output = 1 / (1 + exp(-$child->input));
        }
    }



}


class Layer
{
    public $size;
    public $neurons = [];

    public function __construct($size)
    {
        $this->size = $size;
        $this->createNeurons($size);
    }

    private function createNeurons($size)
    {
        for ($i=0; $i < $size; $i++){
            $this->neurons[] = new Neuron(mt_rand(-100, 100) / 100);
        }
    }

    /*Создаем связи от родителей к детям*/
    public function createRelation(Layer $layer)
    {
        /*Текущие нейроны связываем с последующими*/
        foreach ($this->neurons as $neuron){
            foreach ($layer->neurons as $relationLayerNeuron){
                $neuron->childs[] = $relationLayerNeuron;
            }
        }

    }


    public function activate()
    {
        /*Идем по массиву нейронов текущих*/
        foreach ($this->neurons as $neuron){
            /*Активируем детей*/

            $neuron->activateChild();
        }
    }
}


class NeuralNetwork
{
    public $layers = [];


    public function createLayer($count)
    {
        if (count($this->layers) === 0){
            $this->layers[] = new Layer($count);
        }else{
            $layer = new Layer($count);
            /*У предыдущего слоя вызываем метод создать связь (с текущим) */
            $this->layers[ count($this->layers) - 1 ]->createRelation( $layer );
            $this->layers[] = $layer;
        }
    }

    private function getFirstLayer()
    {
        return $this->layers[0];
    }


    public function setInput(array $array)
    {
        for ($i=0; $i < count($this->getFirstLayer()->neurons)  ; $i++){

//            $this->getFirstLayer()->neurons[$i]->input = mt_rand(-100, 100) / 100;
            $this->getFirstLayer()->neurons[$i]->input = $array[$i];


        }
    }

    public function run()
    {


        foreach ($this->layers as $layer){
            $layer->activate();
        }

    }

    public function getResult()
    {
        $res = [];
        foreach ($this->layers[ count( $this->layers ) - 1 ]->neurons as $neuron){
            $res[] = $neuron->output;
        }
        return $res;
    }

}

$neuralNetwork = new NeuralNetwork();

/*Создаем слои*/
$neuralNetwork->createLayer(3);
$neuralNetwork->createLayer(2);
$neuralNetwork->createLayer(1);
/*вставляем входы*/
$neuralNetwork->setInput([-1,-1,1]);
/*Применяем активацию для всех нейронов кроме начального слоя*/
$neuralNetwork->run();

var_dump($neuralNetwork->getResult());die;


/*Надо Посчитать в нейроне все вхождения из предыдущих нейронов и прогнать через функцию активации*/

