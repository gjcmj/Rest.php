<?php namespace Rest;

use Closure;

/**
 * Pipeline
 *
 * @package Rest
 */
class Pipeline {

    /**
     * The object being passed through the pipeline
     *
     * @var mixed
     */
    protected $passable;

    /**
     * The method to call on each pipe
     *
     * @var string
     */
    protected $method = 'handle';

    /**
     * The array of class pipes
     *
     * @var array
     */
    protected $pipes = [];

    /**
     * Set the object being sent through the pipeline
     *
     * @param  mixed  $passable
     * @return $this
     */
    public function send($passable) {
        $this->passable = $passable;

        return $this;
    }

    /**
     * Set the array of pipes
     * 
     * @param  array|mixed  $pipes
     * @return $this
     */
    public function through($pipes) {
        $this->pipes = is_array($pipes) ? $pipes : func_get_args();

        return $this;
    }

    /**
     * Run the pipeline with a final destination callback
     *
     * @param  \Closure  $destination
     * @return mixed
     */
    public function then(Closure $destination) {
        $pipeline = array_reduce(
            array_reverse($this->pipes), $this->carry(), $destination
        );

        return $pipeline($this->passable);
    }

    /**
     * Get a Closure that represents a slice of the application onion
     *
     * @return \Closure
     */
    public function carry() {
        return function ($stack, $pipe) {
            return function($request) use ($stack, $pipe) {
                return Services::$pipe()->{$this->method}($request, $stack);
            };
        };
    }
}
